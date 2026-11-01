@extends('layouts.portal', ['title' => 'Admin dashboard | Beauty & Blooms'])

@section('content')
    <p class="kicker">Store management</p>
    <h1 class="portal-title">Good day, {{ auth()->user()->name }}.</h1>
    <div class="portal-stat-grid">
        <div class="portal-stat"><span class="portal-muted">All orders</span><strong>{{ $orderCount }}</strong></div>
        <div class="portal-stat"><span class="portal-muted">Awaiting action</span><strong>{{ $pendingCount }}</strong></div>
        <div class="portal-stat"><span class="portal-muted">Products</span><strong>{{ $productCount }}</strong></div>
        <div class="portal-stat"><span class="portal-muted">Revenue from active orders</span><strong>${{ number_format((float) $revenue, 2) }}</strong></div>
    </div>
    <section class="portal-card" style="margin-top:24px">
        <div class="portal-row" style="margin-bottom:18px">
            <h2 class="h2" style="font-size:30px">Recent orders</h2>
            <a class="portal-link" href="{{ route('admin.orders.index') }}">View all orders →</a>
        </div>
        <div class="portal-table-wrap">
            <table class="portal-table">
                <thead><tr><th>Order</th><th>Customer</th><th>Date</th><th>Status</th><th>Total</th><th></th></tr></thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr><td>{{ $order->order_number }}</td><td>{{ $order->customer_name }}</td><td>{{ $order->created_at->format('M j, Y') }}</td><td><span class="portal-badge">{{ $order->status }}</span></td><td>${{ number_format((float) $order->total, 2) }}</td><td><a class="portal-link" href="{{ route('admin.orders.show', $order) }}">Details</a></td></tr>
                    @empty
                        <tr><td colspan="6" class="portal-muted">No orders have been received yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
