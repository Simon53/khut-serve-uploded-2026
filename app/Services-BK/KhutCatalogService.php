<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KhutCatalogService
{
    private const CACHE_KEY = 'khut:catalog';
    private const TTL = 300; // 5 minutes

    /**
     * Fetch fresh catalog from API and store in cache
     */
    public function refresh(): array
    {
        $secret = config('services.khut.secret');
        $baseUrl = config('services.khut.catalog_url');

        // ✅ config validation
        if (!$secret || !$baseUrl) {
            Log::error('KhutCatalogService: Missing config values', [
                'secret' => $secret,
                'baseUrl' => $baseUrl,
            ]);
            return [];
        }

        try {
            $response = Http::timeout(20)->get($baseUrl, [
                'secret_key' => $secret,
            ]);

            // ✅ response debug
            Log::info('KhutCatalogService API call', [
                'url' => $baseUrl,
                'status' => $response->status(),
            ]);

            if (!$response->successful()) {
                Log::warning('KhutCatalogService: API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $payload = $response->json();

            // ✅ important debug
            if (!isset($payload['products'])) {
                Log::warning('KhutCatalogService: Invalid API response', [
                    'payload' => $payload,
                ]);
                return [];
            }

        } catch (\Throwable $e) {
            Log::error('KhutCatalogService: Exception', [
                'message' => $e->getMessage(),
            ]);
            return [];
        }

        // ✅ build map
        $map = [];

        foreach ($payload['products'] as $item) {
            $barcode = trim((string)($item['barcode'] ?? ''));

            if ($barcode === '') {
                continue;
            }

            $map[$barcode] = [
                'stock' => (int)($item['store_stock'] ?? $item['stock'] ?? 0),
                'price' => (float)($item['sale_rate'] ?? 0),
                'name'  => (string)($item['name'] ?? ''),
            ];
        }

        // ✅ store cache
        Cache::put(self::CACHE_KEY, $map, self::TTL);

        Log::info('KhutCatalogService: Catalog refreshed', [
            'total_items' => count($map),
        ]);

        return $map;
    }

    /**
     * Get full catalog (with cache)
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::TTL, function () {
            return $this->refresh();
        });
    }

    /**
     * Check barcode exists
     */
    public function hasBarcode(string $barcode): bool
    {
        $barcode = trim($barcode);

        if ($barcode === '') {
            return false;
        }

        $catalog = $this->all();

        return isset($catalog[$barcode]);
    }

    /**
     * Get stock
     */
    public function getStock(string $barcode): int
    {
        $barcode = trim($barcode);

        if ($barcode === '') {
            return 0;
        }

        $catalog = $this->all();

        return (int)($catalog[$barcode]['stock'] ?? 0);
    }

    /**
     * Get multiple stocks
     */
    public function getStocksForBarcodes(array $barcodes): array
    {
        $catalog = $this->all();
        $result = [];

        foreach ($barcodes as $b) {
            $key = trim((string)$b);

            if ($key === '') {
                continue;
            }

            $result[$key] = (int)($catalog[$key]['stock'] ?? 0);
        }

        return $result;
    }

    /**
     * Decrement stock locally
     */
    public function decrementStock(string $barcode, int $qty): void
    {
        if ($qty <= 0) return;

        $barcode = trim($barcode);

        $catalog = Cache::get(self::CACHE_KEY);

        if (!isset($catalog[$barcode])) return;

        $catalog[$barcode]['stock'] = max(0, (int)$catalog[$barcode]['stock'] - $qty);

        Cache::put(self::CACHE_KEY, $catalog, self::TTL);
    }

    /**
     * Decrement stock for order
     */
    public function decrementForOrder(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            $barcode = trim((string)$item->barcode);

            if ($barcode !== '') {
                $this->decrementStock($barcode, (int)$item->quantity);
            }
        }
    }
}