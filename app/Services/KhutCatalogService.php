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

        $available = (int) $catalog[$barcode]['stock'];
        $catalog[$barcode]['stock'] = max(0, $available - min($qty, $available));
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

    /**
     * Validate cart line items against catalog stock.
     * Returns issue rows when stock is missing or insufficient.
     */
    public function validateCartStock(array $cart): array
    {
        $needed = [];
        $names = [];

        foreach ($cart as $item) {
            $barcode = trim((string) ($item['barcode'] ?? ''));
            $qty = max(1, (int) ($item['qty'] ?? 1));
            $name = (string) ($item['name'] ?? 'Product');

            if ($barcode === '' || $barcode === 'NO_BARCODE') {
                $needed['__missing__'] = ($needed['__missing__'] ?? 0) + $qty;
                $names['__missing__'] = $name;
                continue;
            }

            $needed[$barcode] = ($needed[$barcode] ?? 0) + $qty;
            $names[$barcode] = $name;
        }

        $issues = [];

        foreach ($needed as $barcode => $qty) {
            if ($barcode === '__missing__') {
                $issues[] = [
                    'name' => $names[$barcode],
                    'barcode' => '',
                    'needed' => $qty,
                    'available' => 0,
                ];
                continue;
            }

            $available = $this->hasBarcode($barcode) ? $this->getStock($barcode) : 0;

            if (!$this->hasBarcode($barcode) || $available < $qty) {
                $issues[] = [
                    'name' => $names[$barcode],
                    'barcode' => $barcode,
                    'needed' => $qty,
                    'available' => $available,
                ];
            }
        }

        return $issues;
    }

    public function validateOrderStock(Order $order): array
    {
        $items = $order->relationLoaded('items')
            ? $order->items
            : $order->items()->get();

        $cart = [];
        foreach ($items as $item) {
            $cart[] = [
                'name' => $item->product_name,
                'barcode' => $item->barcode,
                'qty' => $item->quantity,
            ];
        }

        return $this->validateCartStock($cart);
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
}