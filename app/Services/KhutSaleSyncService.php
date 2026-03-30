<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KhutSaleSyncService
{
    /**
     * Push order to KHUT sale_update API.
     * Returns true when API call succeeds (HTTP success + parsable response).
     */
    public function syncOrder(Order $order, int $paymentStatus = 0): bool
    {
        $apiKey = (string) (config('services.khut.sale_update_api_key') ?? '');
        $url = (string) (config('services.khut.sale_update_url') ?? '');

        if ($apiKey === '' || $url === '') {
            Log::warning('KhutSaleSyncService: missing API credentials/url', [
                'order_id' => $order->id,
            ]);
            return false;
        }

        $cacheKey = 'khut:sale-sync:' . $order->id . ':' . $paymentStatus;
        if (!Cache::add($cacheKey, now()->timestamp, now()->addDays(2))) {
            return true;
        }

        $order->loadMissing('items');

        $items = [];
        foreach ($order->items as $item) {
            $sku = trim((string) ($item->barcode ?? ''));
            if ($sku === '') {
                continue;
            }

            $items[] = [
                'sku' => is_numeric($sku) ? (int) $sku : $sku,
                'quantity' => (int) $item->quantity,
                'sale_rate' => (float) $item->price,
            ];
        }

        if ($items === []) {
            Log::warning('KhutSaleSyncService: no valid SKU items to sync', [
                'order_id' => $order->id,
            ]);
            return false;
        }

        $customerName = trim($order->first_name . ' ' . $order->last_name);
        $deliveryAddress = trim(implode(', ', array_filter([
            $order->address,
            $order->apartment,
            $order->city,
            $order->district,
            $order->postcode,
        ])));

        $payload = [
            'api_key' => $apiKey,
            'customer_name' => $customerName ?: 'Customer',
            'contact_number' => (string) ($order->phone ?? ''),
            'order_time' => optional($order->created_at)->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i:s'),
            'invoice_number' => $order->transaction_id ?: ('ORD-' . $order->id),
            'delivery_address' => $deliveryAddress,
            'sale_amount' => (float) $order->subtotal,
            'discount' => 0,
            'delivery_charge' => (float) $order->delivery_charge,
            'vat' => 0,
            'payable_amount' => (float) $order->total,
            'payment_status' => $paymentStatus,
            'items' => $items,
        ];

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->asJson()
                ->post($url, $payload);

            if (!$response->successful()) {
                Log::warning('KhutSaleSyncService: API request failed', [
                    'order_id' => $order->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                Cache::forget($cacheKey);
                return false;
            }

            Log::info('KhutSaleSyncService: order synced successfully', [
                'order_id' => $order->id,
                'payment_status' => $paymentStatus,
                'response' => $response->json() ?? $response->body(),
            ]);
            return true;
        } catch (\Throwable $e) {
            Log::error('KhutSaleSyncService: exception while syncing order', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);
            Cache::forget($cacheKey);
            return false;
        }
    }

    /**
     * Cancel synced order in KHUT cancel_order API by invoice number.
     */
    public function cancelOrder(Order $order): bool
    {
        $apiKey = (string) (config('services.khut.cancel_order_api_key') ?? '');
        $url = (string) (config('services.khut.cancel_order_url') ?? '');

        if ($apiKey === '' || $url === '') {
            Log::warning('KhutSaleSyncService: missing cancel API credentials/url', [
                'order_id' => $order->id,
            ]);
            return false;
        }

        $invoiceNumber = $order->transaction_id ?: ('ORD-' . $order->id);
        $cacheKey = 'khut:cancel-sync:' . $invoiceNumber;
        if (!Cache::add($cacheKey, now()->timestamp, now()->addDays(2))) {
            return true;
        }

        $payload = [
            'api_key' => $apiKey,
            'invoice_number' => $invoiceNumber,
        ];

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->asJson()
                ->post($url, $payload);

            if (!$response->successful()) {
                Log::warning('KhutSaleSyncService: cancel API request failed', [
                    'order_id' => $order->id,
                    'invoice_number' => $invoiceNumber,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                Cache::forget($cacheKey);
                return false;
            }

            Log::info('KhutSaleSyncService: order cancel synced successfully', [
                'order_id' => $order->id,
                'invoice_number' => $invoiceNumber,
                'response' => $response->json() ?? $response->body(),
            ]);
            return true;
        } catch (\Throwable $e) {
            Log::error('KhutSaleSyncService: exception while cancel syncing order', [
                'order_id' => $order->id,
                'invoice_number' => $invoiceNumber,
                'message' => $e->getMessage(),
            ]);
            Cache::forget($cacheKey);
            return false;
        }
    }
}

