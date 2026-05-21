<?php
namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\KhutCatalogService;

class OrderController extends Controller
{
   
    /*public function index(){
        $orders = Order::with('items') // ✅ add this
            ->orderByRaw("CASE WHEN delivery_status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('id', 'desc')
            ->paginate(10);
    
        return view('orders.orders', compact('orders'));
    }*/
    
    public function index(Request $request) {
        $search = $request->input('search');
    
        $query = Order::with('items');
    
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
               // 1. Order ID Search (Perfect match or Like search)
                $q->where('id', 'LIKE', "%{$search}%")
                
                // 2. Customer Name Search (Full Name Concat)
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  
                // 3. Phone Search
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  
                // 4. Date Search (DD-MM-YYYY format support)
                  ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereDate('created_at', 'LIKE', "%{$search}%")
    
                // 5. Product Name Search
                  ->orWhereHas('items', function($iq) use ($search) {
                      $iq->where('product_name', 'LIKE', "%{$search}%");
                  });
            });
        }
    
        $orders = $query->orderByRaw("CASE WHEN delivery_status = 'pending' THEN 0 ELSE 1 END")
                        ->orderBy('id', 'desc')
                        ->paginate(10)
                        ->withQueryString();
    
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


    /*public function updateDeliveryStatus(Request $request, $id){
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
    }*/


    public function updateDeliveryStatus(Request $request, $id) {
            $request->validate([
                'delivery_status' => 'required|in:pending,delivered,cancel,confirmed',
            ]);

            // Order এবং এর Items লোড করা হচ্ছে
            $order = Order::with('items')->find($id);

            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }

            $oldStatus = $order->delivery_status;
            $newStatus = $request->input('delivery_status');

            // যদি স্ট্যাটাস পরিবর্তন না হয়, তবে কিছু করার দরকার নেই
            if ($oldStatus === $newStatus) {
                return response()->json(['success' => true, 'message' => 'Status is already ' . $newStatus]);
            }

            // --- আপনার কাঙ্ক্ষিত স্টক এবং API সিঙ্ক লজিক শুরু ---
            
            if ($newStatus === 'cancel' && $oldStatus !== 'cancel') {
                // ১. সার্ভিসগুলো রিজলভ করা
                $catalogService = app(\App\Services\KhutCatalogService::class);
                $syncService = app(\App\Services\KhutSaleSyncService::class);

                // ২. স্টক আবার ক্যাশে ফেরত পাঠানো
                $catalogService->incrementForOrder($order);
                
                // ৩. থার্ড পার্টি সিস্টেমে ক্যানসেল রিকোয়েস্ট পাঠানো
                $syncService->cancelOrder($order);

                $stockMessage = " & Stock Restored";
            } 
            
            // যদি ভুল করে ক্যানসেল করা অর্ডার আবার একটিভ করেন
            elseif ($oldStatus === 'cancel' && $newStatus !== 'cancel') {
                $catalogService = app(\App\Services\KhutCatalogService::class);
                $catalogService->decrementForOrder($order);
                
                $stockMessage = " & Stock Re-deducted";
            } else {
                $stockMessage = "";
            }

            // --- লজিক শেষ ---

            // ডাটাবেজে নতুন স্ট্যাটাস সেভ
            $order->delivery_status = $newStatus;
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Order ' . $newStatus . $stockMessage
            ]);
        }
}