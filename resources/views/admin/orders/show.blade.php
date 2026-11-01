@extends('layouts.portal', ['title' => 'Order '.$order->order_number.' | Admin'])

@section('content')
    <div class="portal-row" style="margin-bottom:22px">
        <div><p class="kicker">Order details</p><h1 class="portal-title" style="margin-bottom:0">{{ $order->order_number }}.</h1></div>
        <a class="portal-link" href="{{ route('admin.orders.index') }}">← Back to orders</a>
    </div>
    <div class="portal-grid">
        <section class="portal-card">
            <h2 class="h2" style="font-size:30px;margin-bottom:15px">Delivery</h2>
            <p><strong>{{ $order->customer_name }}</strong><br>{{ $order->customer_email }}<br>{{ $order->phone }}</p>
            <p style="margin-top:14px">{{ $order->address_line }}<br>{{ $order->city }}{{ $order->state ? ', '.$order->state : '' }} {{ $order->postal_code }}<br>{{ $order->country }}</p>
            @if ($order->notes)<p class="portal-muted" style="margin-top:14px">Note: {{ $order->notes }}</p>@endif
        </section>
        <section class="portal-card">
            <h2 class="h2" style="font-size:30px;margin-bottom:15px">Order status</h2>
            <form class="portal-form" method="post" action="{{ route('admin.orders.update', $order) }}">
                @csrf @method('patch')
                <div class="portal-field"><label for="status">Status</label><select id="status" name="status" @disabled($order->status === 'cancelled')>@foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)<option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
                <p class="portal-muted">Payment: {{ $order->payment_method === 'demo_card' ? 'Demo card (not charged)' : 'Cash on delivery' }} · {{ $order->payment_status === 'demo_paid' ? 'Demo payment simulated' : ucfirst($order->payment_status) }}</p>
                @if ($order->status !== 'cancelled')<button class="portal-button" type="submit">Update status</button>@endif
            </form>
        </section>
    </div>
    <section class="portal-card portal-table-wrap">
        <h2 class="h2" style="font-size:30px;margin-bottom:15px">Items</h2>
        <table class="portal-table">
            <thead><tr><th>Product</th><th>Option</th><th>Unit price</th><th>Qty</th><th>Total</th></tr></thead>
            <tbody>@foreach ($order->items as $item)<tr><td>{{ $item->product_name }}</td><td>{{ $item->product_option }}</td><td>${{ number_format((float) $item->unit_price, 2) }}</td><td>{{ $item->quantity }}</td><td>${{ number_format((float) $item->line_total, 2) }}</td></tr>@endforeach</tbody>
        </table>
        <div style="max-width:320px;margin:20px 0 0 auto">
            <div class="portal-row"><span>Subtotal</span><strong>${{ number_format((float) $order->subtotal, 2) }}</strong></div>
            <div class="portal-row" style="margin-top:8px"><span>Delivery</span><strong>${{ number_format((float) $order->shipping_cost, 2) }}</strong></div>
            <div class="portal-row" style="margin-top:12px"><strong>Total</strong><strong>${{ number_format((float) $order->total, 2) }}</strong></div>
        </div>
    </section>
@endsection
