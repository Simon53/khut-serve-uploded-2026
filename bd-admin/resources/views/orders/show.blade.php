@extends('layout.app')
@section('title', 'Khut::Order Details')
@section('content')
   <div class="container-fluid">
    <div class="row mb-3 card p-3" >
        <div class="col-md-12 p-3 text-center" ><img style="width:100px;" src="{{ asset('images/logo.png') }}" alt="Logo"></div>
        <div class="col-md-12" style="color:#000">
            <h4>Order #{{ $order->id }} Details</h4>
            <h5>Customer Info</h5>
            <p><strong>Name:</strong>  {{ $order->first_name }} {{ $order->last_name }}</p>
            <p><strong>Phone:</strong>  {{ $order->phone }}</p>
            <p><strong>Email:</strong>  {{ $order->email }}</p>
            <p><strong>Address:</strong>  {{ $order->address }}, {{ $order->city }}, {{ $order->district }}, {{ $order->postcode }}</p>
            <p><strong>Note:</strong> {{ $order->notes ?? '-' }}</p>
            <p style="display:none"><strong>Payment Method:</strong>  {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Paid' }}</p>
            <p><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y h:i A') }}</p>
            <p><strong>Status:</strong>  {{ ucfirst($order->status) }}</p>
        </div>

         <h5>Order Items</h5>
         <div class="table-responsive col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Qty</th>
                        <th>Barcode</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                         <td>
                            @php
                                $img = $item->product?->main_image
                                    ? env('ADMIN_BASE_URL') . '/storage/' . $item->product->main_image
                                    : asset('no-image.png');
                            @endphp

                            <img src="{{ $img }}"
                                style="border-radius:4px; width:40px; height:auto">
                        </td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->size ?? '-' }}</td>
                        <td>{{ $item->color ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->barcode }}</td>
                        <td>৳ {{ $item->price }}</td>
                        <td>৳ {{ $item->subtotal }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="7" class="text-right">Subtotal</th>
                        <th>৳ {{ $order->subtotal }}</th>
                    </tr>
                    <tr>
                        <th colspan="7" class="text-right">Delivery</th>
                        <th>৳ {{ $order->delivery_charge }}</th>
                    </tr>
                    <tr>
                        <th colspan="7" class="text-right">Total</th>
                        <th>৳ {{ $order->total }}</th>
                    </tr>
                </tfoot>
            </table>
         </div>        
        <div class="p-2 text-center">
            <a href="{{ route('orders.downloadPdf', $order->id) }}" 
                class="btn btn-success mb-3 col-md-2" target="_blank">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
        </div>    
    </div>

   
</div>

@endsection  


@section('script')

@endsection