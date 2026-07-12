@extends('layout.app')

@section('title', 'Khut::Order List')

@section('content')

<style>
    .table-hover tbody tr:hover {
        background-color: #dfd8d8;
    }

    /* Delivery Badge */
    .delivery-badge {
        padding: 5px 10px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        cursor: pointer;
    }
    .bg-pending { background-color: orange; }
    .bg-confirmed { background-color: #007bff; }
    .bg-delivered { background-color: green; }
    .bg-cancel { background-color: red; }

    .delivery-status { display: none; }

    /* Payment Status */
    .status-badge {
        padding: 5px 10px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
    }

    /* 👉 BOTH SAME GREEN */
    .bg-cod { background-color: #4caf50; }
    .bg-paid { background-color: #4caf50; }

    /* Cancel */
    .bg-cancel { background-color: #ff0000; }

    .inside-dhaka {
        background-color: #d4edda;
        color: #155724;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: 500;
    }

    .outside-dhaka {
        background-color: #f8d7da;
        color: #721c24;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: 500;
    }
</style>

<div class="container-fluid">
    <div class="">
        <div class="card p-4 col-md-12 table-responsive" style="color:#333">
            <h4>Order List</h4>
                <div class="col-md-12">
                    <form action="{{ route('orders.index') }}" method="GET" class="float-right form-inline col-md-6 mb-2">
                        <div class="input-group col-md-12" >
                           <input type="text" name="search" class="form-control " 
                               placeholder="Search by Order Id, Customer Name, Contact No., Product Name or DD-MM-YYYY" 
                               value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-search"></i> Search
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Clear</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
           

            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>SL</th>
                        <th>Order ID</th>
                        <th>Image</th>
                        <th>Customer</th>
                        <th>Product Name</th>
                        <th>Order Date</th>
                        <th>Contact No</th>
                        <th>Alternative Contact No</th>
                        <th>Side</th>
                        <th>District Name</th>
                        <th>Total</th>
                        <th>Payment Status</th>
                        <th>Delivery Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($orders as $order)
                    <tr id="orderRow{{ $order->id }}">
                        <td>{{ $loop->iteration }}</td>

                        
                         <td>
                            <a href="{{ route('orders.show', $order->id) }}">
                                {{ $order->id }} 
                            </a>
                        </td>

                       @php
                            $firstItem = $order->items->first();
                            $product = $firstItem?->product;
                        
                            $baseUrl = 'https://khut.shop/bd-admin/public';
                        
                            $image = ($product && $product->main_image)
                                ? $baseUrl . '/storage/' . ltrim($product->main_image, '/')
                                : asset('no-image.png');
                        @endphp

                        <td>
                            <img src="{{ $image }}"
                                style="border-radius:4px; width:60px; height:auto">
                        </td>
                         
                        <td>
                            <a href="{{ route('orders.show', $order->id) }}">
                                {{ $order->first_name }} {{ $order->last_name }}
                            </a>
                        </td>
                        
                        
                        
                        <td>
                            @foreach($order->items as $item)
                                <div>{{ $item->product_name }}</div>
                            @endforeach
                        </td>

                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y h:i A') }}</td>

                        <td>{{ $order->phone }}</td>
                        <td>{{ $order->alternative_phone }}</td>
                        
                        <td>
                            @php
                                $district = strtolower($order->district ?? '');
                            @endphp
                        
                            @if(str_contains($district, 'dhaka'))
                                <span class="inside-dhaka">Inside Dhaka</span>
                            @else
                                <span class="outside-dhaka">Outside Dhaka</span>
                            @endif
                        </td>
                        
                        <td>{{ $order->district }}</td>

                        <td>৳ {{ number_format($order->total, 2) }}</td>

                        <!-- ✅ Payment Status -->
                        <td> 
                            <span class="status-badge 
                                {{ $order->delivery_status == 'cancel' ? 'bg-cancel' : 'bg-paid' }}"
                                data-original="{{ ucfirst($order->status) }}">
                                
                                {{ $order->delivery_status == 'cancel' ? 'Order Cancel' : ucfirst($order->status) }}
                            </span> 
                        </td>

                        <!-- ✅ Delivery Status -->
                        <td>
                            <span class="delivery-badge 
                                {{ 
                                    $order->delivery_status == 'pending' ? 'bg-pending' : 
                                    ($order->delivery_status == 'confirmed' ? 'bg-confirmed' : 
                                    ($order->delivery_status == 'delivered' ? 'bg-delivered' : 'bg-cancel')) 
                                }}"
                                data-id="{{ $order->id }}">
                                {{ ucfirst($order->delivery_status) }}
                            </span>

                            <select class="form-control delivery-status" data-id="{{ $order->id }}">
                                <option value="pending" {{ $order->delivery_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $order->delivery_status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="delivered" {{ $order->delivery_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancel" {{ $order->delivery_status == 'cancel' ? 'selected' : '' }}>Cancel</option>
                            </select>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div>
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){

    // ১. ব্যাজ এ ক্লিক করলে সিলেক্ট বক্স দেখাবে
    $(document).on('click', '.delivery-badge', function() {
        let badge = $(this);
        let select = badge.siblings('select.delivery-status');
        
        // সব খোলা সিলেক্ট বক্স বন্ধ করে শুধু এটি ওপেন করা
        $('.delivery-status').hide();
        $('.delivery-badge').show();
        
        badge.hide();
        select.show().focus();
    });

    // ২. ডেলিভারি স্ট্যাটাস পরিবর্তন করলে যা হবে
    $(document).on('change', '.delivery-status', function() {
        let select = $(this);
        let badge = select.siblings('.delivery-badge');
        let orderId = select.data('id');
        let status  = select.val();
        let statusText = status.charAt(0).toUpperCase() + status.slice(1);

        // সুন্দর কনফার্মেশন পপআপ (ব্রাউজার alert/confirm এর বদলে)
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to change the status to "${statusText}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                let updateUrl = "{{ url('admin/orders') }}/:id/delivery-status".replace(':id', orderId);

                $.ajax({
                    url: updateUrl,
                    type: 'PATCH',
                    data: { delivery_status: status },
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        
                        // সাকসেস টোস্ট মেসেজ (কন্ট্রোলার থেকে আসা স্টক মেসেজটি এখানে দেখাবে)
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: res.message, 
                            timer: 3000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });

                        // Delivery badge কালার এবং টেক্সট আপডেট
                        badge.text(statusText)
                             .removeClass('bg-pending bg-confirmed bg-delivered bg-cancel')
                             .addClass(
                                status == 'pending' ? 'bg-pending' :
                                status == 'confirmed' ? 'bg-confirmed' :
                                status == 'delivered' ? 'bg-delivered' :
                                'bg-cancel'
                             );

                        // Payment status ব্যাজ আপডেট লজিক
                        let paymentBadge = badge.closest('tr').find('.status-badge');
                        if(status === 'cancel'){
                            paymentBadge.text('Order Cancel')
                                        .removeClass('bg-paid bg-cod')
                                        .addClass('bg-cancel');
                        } else {
                            let original = paymentBadge.data('original');
                            paymentBadge.text(original)
                                        .removeClass('bg-cancel')
                                        .addClass('bg-paid');
                        }

                        select.hide();
                        badge.show();
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong while updating.', 'error');
                        // এরর হলে আগের ভ্যালুতে ফিরিয়ে নেয়া
                        select.val(badge.text().trim().toLowerCase());
                        select.hide();
                        badge.show();
                    }
                });
            } else {
                // ইউজার ক্যানসেল করলে সিলেক্ট বক্স হাইড করে আগের ব্যাজ দেখানো
                select.val(badge.text().trim().toLowerCase());
                select.hide();
                badge.show();
            }
        });
    });

    // ৩. সিলেক্ট বক্সের বাইরে ক্লিক করলে সেটি অটো হাইড হয়ে যাবে
    $(document).click(function(event) { 
        if(!$(event.target).closest('.delivery-badge, .delivery-status').length) {
            $('.delivery-status').hide();
            $('.delivery-badge').show();
        }        
    });
});
</script>


@endsection