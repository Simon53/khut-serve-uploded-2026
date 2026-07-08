<?php

namespace App\Services;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderEmailService
{
    public function sendOrderConfirmation(Order $order): void
    {
        $order->loadMissing('items');

        $email = trim((string) ($order->email ?? ''));
        if ($email === '') {
            return;
        }

        $cacheKey = 'order_confirmation_email_' . $order->id;

        if (!Cache::add($cacheKey, true, now()->addDays(7))) {
            return;
        }

        try {
            Mail::to($email)->send(new OrderConfirmationMail($order));
        } catch (\Throwable $e) {
            Cache::forget($cacheKey);

            Log::warning('Order confirmation email failed', [
                'order_id' => $order->id,
                'email' => $email,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
