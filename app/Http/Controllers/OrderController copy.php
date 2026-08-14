<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\KhutCatalogService;
use App\Services\KhutSaleSyncService;
use App\Services\OrderEmailService;

class OrderController extends Controller
{
   public function store(Request $request){
    $cart = json_decode($request->cart, true);

    if(!$cart || count($cart) === 0){
        return redirect()->route('checkout.index')->with('error', 'Cart is empty!');
    }

    $stockIssues = app(KhutCatalogService::class)->validateCartStock($cart);
    if (!empty($stockIssues)) {
        return redirect()->route('checkout.index')->with('stock_error', $stockIssues);
    }

    $subtotal = 0;
    foreach($cart as $item){
        $subtotal += $item['qty'] * $item['price'];
    }

    $postcode = (int)$request->postcode;
    $delivery = 150;
    
    if ($postcode == 1000 || $postcode == 1100 || ($postcode >= 1203 && $postcode <= 1236)) {
        $delivery = 80;
    }

    $status = 'Cash on Delivery';
    $deliveryStatus = 'Cash on Delivery';

    $order = Order::create([
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'email'      => $request->email,
        'phone'      => $request->phone,
        'alternative_phone' => $request->alternative_phone ?? null,
        'address'    => $request->address,
        'apartment'  => $request->apartment ?? null,
        'district'   => $request->district,
        'city'       => $request->city,
        'postcode'   => $request->postcode,
        'country'    => $request->country ?? 'Bangladesh',
        'notes'      => $request->note ?? null,
        'payment_method' => $request->payment_method,
        'subtotal'   => $subtotal,
        'delivery_charge' => $delivery,
        'total'      => $subtotal + $delivery,
        'status'     => $status,
        'delivery_status' => $deliveryStatus, 
    ]);

    foreach($cart as $item){
        $order->items()->create([
            'product_id'   => $item['id'] ?? null,
            'product_name' => $item['name'],
            'size'         => $item['size'] ?? null,
            'color'        => $item['color'] ?? null,
            'quantity'     => $item['qty'],
            'price'        => $item['price'],
            'subtotal'     => $item['qty'] * $item['price'],
            'barcode'      => $item['barcode'] ?? null,
        ]);
    }

    $order->load('items');
    $stockIssues = app(KhutCatalogService::class)->validateOrderStock($order);
    if (!empty($stockIssues)) {
        $order->items()->delete();
        $order->delete();

        return redirect()->route('checkout.index')->with('stock_error', $stockIssues);
    }

    app(KhutCatalogService::class)->decrementForOrder($order);

    try {
        app(KhutSaleSyncService::class)->syncOrder($order, 0);
    } catch (\Throwable $e) {
        \Log::warning('COD order external sync failed', [
            'order_id' => $order->id,
            'message' => $e->getMessage(),
        ]);
    }

    app(OrderEmailService::class)->sendOrderConfirmation($order);

    $request->session()->put('order', $order);

    return redirect()->route('payment.success', ['order_id' => $order->id]);
}

}


