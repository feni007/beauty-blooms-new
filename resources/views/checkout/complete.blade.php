@extends('layouts.portal', ['title' => 'Order received | Beauty & Blooms'])

@section('content')
    <section class="portal-card">
        <p class="kicker">Thank you</p>
        <h1 class="portal-title">Your order is confirmed.</h1>
        <p>Your order number is <strong>{{ $order->order_number }}</strong>. We’ll email order updates to {{ $order->customer_email }}.</p>
        <div class="portal-table-wrap" style="margin:24px 0">
            <table class="portal-table">
                <thead><tr><th>Product</th><th>Option</th><th>Quantity</th><th>Total</th></tr></thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr><td>{{ $item->product_name }}</td><td>{{ $item->product_option }}</td><td>{{ $item->quantity }}</td><td>${{ number_format((float) $item->line_total, 2) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="portal-muted">
            Order total: ${{ number_format((float) $order->total, 2) }} ·
            @if ($order->payment_method === 'demo_card')
                Demo card payment simulated successfully (no real charge).
            @else
                Cash on delivery.
            @endif
        </p>
        <a class="portal-button" href="{{ route('orders.index') }}" style="margin-top:22px">View my orders</a>
    </section>
    <script>try { localStorage.removeItem('bb-cart'); } catch (error) { console.error('Unable to clear the saved shopping bag.', error); }</script>
@endsection
