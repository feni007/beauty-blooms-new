@extends('layouts.portal', ['title' => 'Orders | Admin'])

@section('content')
    <div class="portal-row" style="margin-bottom:22px">
        <div><p class="kicker">Fulfilment</p><h1 class="portal-title" style="margin-bottom:0">Orders.</h1></div>
        <a class="portal-link" href="{{ route('admin.dashboard') }}">← Dashboard</a>
    </div>
    <section class="portal-card portal-table-wrap">
        <table class="portal-table">
            <thead><tr><th>Order</th><th>Customer</th><th>Date</th><th>Status</th><th>Payment</th><th>Total</th><th></th></tr></thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr><td>{{ $order->order_number }}</td><td>{{ $order->customer_name }}<br><span class="portal-muted">{{ $order->customer_email }}</span></td><td>{{ $order->created_at->format('M j, Y') }}</td><td><span class="portal-badge">{{ $order->status }}</span></td><td>{{ $order->payment_method === 'demo_card' ? 'Demo (not charged)' : 'Cash on delivery' }}</td><td>${{ number_format((float) $order->total, 2) }}</td><td><a class="portal-link" href="{{ route('admin.orders.show', $order) }}">Details</a></td></tr>
                @empty
                    <tr><td colspan="7" class="portal-muted">No orders have been received yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:20px">{{ $orders->links() }}</div>
    </section>
@endsection
