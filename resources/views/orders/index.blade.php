@extends('layouts.portal', ['title' => 'My orders | Beauty & Blooms'])

@section('content')
    <div class="portal-row" style="margin-bottom:22px">
        <div><p class="kicker">Your account</p><h1 class="portal-title" style="margin-bottom:0">My orders.</h1></div>
        <a class="portal-button" href="{{ route('home') }}">Continue shopping</a>
    </div>
    <section class="portal-card portal-table-wrap">
        <table class="portal-table">
            <thead><tr><th>Order</th><th>Date</th><th>Items</th><th>Status</th><th>Total</th></tr></thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->created_at->format('M j, Y') }}</td>
                        <td>{{ $order->items->sum('quantity') }}</td>
                        <td><span class="portal-badge">{{ $order->status }}</span></td>
                        <td>${{ number_format((float) $order->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="portal-muted">You haven’t placed an order yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:20px">{{ $orders->links() }}</div>
    </section>
@endsection
