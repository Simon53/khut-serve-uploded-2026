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
    <div class="row">
        <div class="card p-4 col-md-12 table-responsive" style="color:#333">
            <h4>Order List</h4>

            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>SL</th>
                        <th>Customer</th>
                        <th>Product Name</th>
                        <th>Order Date</th>
                        <th>Phone</th>
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
<script>
$(document).ready(function(){

    // Click badge → show select
    $(document).on('click', '.delivery-badge', function() {
        let badge = $(this);
        let select = badge.siblings('select.delivery-status');

        badge.hide();
        select.show().focus();
    });

    // Change delivery status
    $(document).on('change', '.delivery-status', function() {
        let select = $(this);
        let badge = select.siblings('.delivery-badge');
        let orderId = select.data('id');
        let status  = select.val();
        let statusText = status.charAt(0).toUpperCase() + status.slice(1);

        if(!confirm(`Are you sure you want to mark this order as "${statusText}"?`)) {
            select.val(badge.text().toLowerCase());
            select.hide();
            badge.show();
            return;
        }

        let updateUrl = "{{ url('admin/orders') }}/:id/delivery-status".replace(':id', orderId);

        $.ajax({
            url: updateUrl,
            type: 'PATCH',
            data: { delivery_status: status },
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(res) {

                alert(res.message);

                // 👉 Delivery badge update
                badge.text(statusText)
                     .removeClass('bg-pending bg-confirmed bg-delivered bg-cancel')
                     .addClass(
                        status == 'pending' ? 'bg-pending' :
                        status == 'confirmed' ? 'bg-confirmed' :
                        status == 'delivered' ? 'bg-delivered' :
                        'bg-cancel'
                     );

                // 👉 Payment status update
                let paymentBadge = badge.closest('tr').find('.status-badge');

                if(status === 'cancel'){
                    paymentBadge
                        .text('Order Cancel')
                        .removeClass('bg-paid bg-cod')
                        .addClass('bg-cancel');
                } else {
                    let original = paymentBadge.data('original');

                    paymentBadge
                        .text(original)
                        .removeClass('bg-cancel')
                        .addClass('bg-paid');
                }

                select.hide();
                badge.show();
            },
            error: function() {
                alert("Something went wrong.");
                select.hide();
                badge.show();
            }
        });
    });

    // Click outside → hide select
    $(document).click(function(event) { 
        if(!$(event.target).closest('.delivery-badge, .delivery-status').length) {
            $('.delivery-status').hide();
            $('.delivery-badge').show();
        }        
    });
});
</script>
@endsection