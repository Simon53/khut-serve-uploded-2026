<?php
namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
   /* public function index(){
    $orders = Order::orderByRaw("CASE WHEN delivery_status = 'pending' THEN 0 ELSE 1 END")
                   ->orderBy('id', 'desc')
                   ->paginate(10);

        return view('orders.orders', compact('orders'));
    }*/
    
    public function index(){
        $orders = Order::with('items') // ✅ add this
            ->orderByRaw("CASE WHEN delivery_status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('id', 'desc')
            ->paginate(10);
    
        return view('orders.orders', compact('orders'));
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['success' => true, 'message' => 'Order deleted successfully']);
    }

    public function latest()
    {
        $orders = Order::latest()->take(10)->get();
        return view('orders.partials.orders_table', compact('orders'))->render();
    }

    public function show(Order $order)
    {
        $order->load('items'); // Load order items
        return view('orders.show', compact('order'));
    }


    public function downloadPdf($id)
    {
        $order = Order::with('items')->findOrFail($id);

        $pdf = Pdf::loadView('orders.pdf', compact('order'))->setPaper('a4', 'portrait');

        return $pdf->download('order_'.$order->id.'.pdf');
    }


    public function updateDeliveryStatus(Request $request, $id){
        // If request is JSON, Laravel can still read via input()
        $request->validate([
            'delivery_status' => 'required|in:pending,delivered,cancel,confirmed',
        ]);
    
        $order = Order::find($id);
        if(!$order){
            return response()->json(['success' => false,'message' => 'Order not found'], 404);
        }
    
        // Use input() to read JSON payload
        $order->delivery_status = $request->input('delivery_status');
        $order->save();
    
        return response()->json(['success' => true,'message' => 'Delivery status updated successfully']);
    }
}