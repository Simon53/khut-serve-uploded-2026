<?php

namespace App\Http\Controllers;

use App\Services\KhutCatalogService;
use Illuminate\Support\Facades\Http;

class StockController extends Controller
{
    
    
    public function testDirectApi()
    {
        $secret = '84d8984a7281de1b58bb6b9f511a81688fc405eeb87cb30153576a3bcaf938fc';
        $url = 'https://khut.bdsoft.us/api/get_all_items.php';

        try {
            $response = Http::timeout(15)->get($url, [
                'secret_key' => $secret,
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            $data = $response->json();

            return response()->json([
                'success' => true,
                'count' => count($data['products'] ?? []),
                'sample' => array_slice($data['products'] ?? [], 0, 5), 
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    
    /**
     * Fetch stock quantity from catalog for a single SKU (barcode).
     * Returns int (0 if unavailable).
     */
    public function getStockQuantity(string $sku): int
        {
            $sku = ltrim(trim($sku), '0'); // normalize barcode
            if ($sku === '') {
                return 0;
            }
        
            return app(KhutCatalogService::class)->getStock($sku);
        }

    /**
     * Fetch stock for multiple SKUs from the catalog. Returns [sku => quantity].
     */
    public function getStocksForSkus(array $skus): array
    {
        return app(KhutCatalogService::class)->getStocksForBarcodes($skus);
    }

    /**
     * HTTP endpoint used by product details page JS: /stock/{sku}
     * Reads from the cached catalog. Barcode not in API → in_catalog: false, show unavailable.
     */
    public function getStock($sku)
    {
        $sku = trim((string)$sku);

        if ($sku === '') {
            return response()->json([
                'success' => false,
                'data' => ['store_stock' => 0, 'in_catalog' => false],
            ], 400);
        }

        $catalog = app(KhutCatalogService::class);
        $inCatalog = $catalog->hasBarcode($sku);
        $qty = $inCatalog ? $catalog->getStock($sku) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'store_stock' => (int) $qty,
                'in_catalog' => $inCatalog,
            ],
        ]);
    }
}
