<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KhutCatalogService
{
    private const CACHE_KEY = 'khut:catalog';
    private const TTL = 300;

    public function refresh(): array
    {
        $secret = (string) (config('services.khut.secret') ?? '');
        $baseUrl = (string) (config('services.khut.catalog_url') ?? '');

        if ($secret === '' || $baseUrl === '') {
            Log::warning('KhutCatalogService: missing config values');
            return [];
        }

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mozilla/5.0',
                ])
                ->get($baseUrl, [
                    'secret_key' => $secret,
                ]);

            if (!$response->successful()) {
                Log::warning('KhutCatalogService: API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $payload = $response->json();
            if (!is_array($payload) || !isset($payload['products']) || !is_array($payload['products'])) {
                Log::warning('KhutCatalogService: Empty API data');
                return [];
            }
        } catch (\Throwable $e) {
            Log::error('KhutCatalogService: Exception', [
                'message' => $e->getMessage(),
            ]);
            return [];
        }

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

        Cache::put(self::CACHE_KEY, $map, self::TTL);
        return $map;
    }

    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::TTL, function () {
            return $this->refresh();
        });
    }

    public function hasBarcode(string $barcode): bool
    {
        $barcode = trim($barcode);
        if ($barcode === '') {
            return false;
        }
        $catalog = $this->all();
        return isset($catalog[$barcode]);
    }

    public function getStock(string $barcode): int
    {
        $barcode = trim($barcode);
        if ($barcode === '') {
            return 0;
        }
        $catalog = $this->all();
        return (int) ($catalog[$barcode]['stock'] ?? 0);
    }

    public function decrementStock(string $barcode, int $qty): void
    {
        if ($qty <= 0) {
            return;
        }

        $barcode = trim($barcode);
        $catalog = Cache::get(self::CACHE_KEY, []);

        if (!isset($catalog[$barcode])) {
            return;
        }

        $catalog[$barcode]['stock'] = max(0, (int)$catalog[$barcode]['stock'] - $qty);
        Cache::put(self::CACHE_KEY, $catalog, self::TTL);
    }

    public function getStocksForBarcodes(array $barcodes): array
    {
        $catalog = $this->all();
        $result = [];

        foreach ($barcodes as $barcode) {
            $key = trim((string) $barcode);
            if ($key === '') {
                continue;
            }
            $result[$key] = (int) ($catalog[$key]['stock'] ?? 0);
        }

        return $result;
    }

    public function decrementForOrder(Order $order): void
    {
        $items = $order->relationLoaded('items')
            ? $order->items
            : $order->items()->get();

        foreach ($items as $item) {
            $barcode = trim((string) ($item->barcode ?? ''));
            if ($barcode === '') {
                continue;
            }
            $this->decrementStock($barcode, (int) $item->quantity);
        }
    }

   
    //cancel order, restore stock

    public function incrementStock(string $barcode, int $qty): void{
            if ($qty <= 0) {
                return;
            }

            $barcode = trim($barcode);
            $catalog = Cache::get(self::CACHE_KEY, []);

            if (!isset($catalog[$barcode])) {
                return;
            }

            // স্টক বৃদ্ধি করা হচ্ছে
            $catalog[$barcode]['stock'] = (int)$catalog[$barcode]['stock'] + $qty;
            Cache::put(self::CACHE_KEY, $catalog, self::TTL);
        }

        public function incrementForOrder(Order $order): void
        {
            $items = $order->relationLoaded('items')
                ? $order->items
                : $order->items()->get();

            foreach ($items as $item) {
                $barcode = trim((string) ($item->barcode ?? ''));
                if ($barcode === '') {
                    continue;
                }
                $this->incrementStock($barcode, (int) $item->quantity);
            }
        }
}