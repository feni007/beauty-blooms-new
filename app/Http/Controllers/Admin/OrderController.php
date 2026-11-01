<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'orderCount' => Order::count(),
            'pendingCount' => Order::where('status', 'pending')->count(),
            'productCount' => Product::count(),
            'revenue' => Order::whereIn('status', ['processing', 'shipped', 'delivered'])->sum('total'),
            'recentOrders' => Order::with('user')->latest()->take(8)->get(),
        ]);
    }

    public function index(): View
    {
        $orders = Order::with('user')->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'processing', 'shipped', 'delivered', 'cancelled'])],
        ]);

        DB::transaction(function () use ($order, $data): void {
            $lockedOrder = Order::query()->lockForUpdate()->with('items.product')->findOrFail($order->id);
            if ($lockedOrder->status === 'cancelled' && $data['status'] !== 'cancelled') {
                throw ValidationException::withMessages([
                    'status' => 'Cancelled orders cannot be reopened.',
                ]);
            }
            if (in_array($lockedOrder->status, ['shipped', 'delivered'], true) && $data['status'] === 'cancelled') {
                throw ValidationException::withMessages([
                    'status' => 'Orders that have shipped or been delivered cannot be cancelled.',
                ]);
            }

            if ($lockedOrder->status !== 'cancelled' && $data['status'] === 'cancelled') {
                foreach ($lockedOrder->items as $item) {
                    $product = Product::query()->whereKey($item->product_id)->lockForUpdate()->first();
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }

            $lockedOrder->update(['status' => $data['status']]);
        });

        return back()->with('success', 'Order status updated.');
    }
}
