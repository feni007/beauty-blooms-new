<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(): View
    {
        return view('checkout.create', [
            'products' => Product::query()->where('is_active', true)->get(['id', 'name', 'price']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'address_line' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:demo_card'],
            'demo_cardholder' => ['required', 'string', 'max:100'],
            'demo_card_number' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value) || preg_replace('/\s+/', '', $value) !== '4242424242424242') {
                        $fail('Use the demo test card number 4242 4242 4242 4242.');
                    }
                },
            ],
            'demo_card_expiry' => [
                'required',
                'string',
                'regex:/^(0[1-9]|1[0-2])\/\d{2}$/',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value) || ! preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $value)) {
                        return;
                    }

                    $expiry = CarbonImmutable::createFromFormat('!m/y', $value)->endOfMonth();
                    if ($expiry->isBefore(now()->startOfDay())) {
                        $fail('The demo card must have a valid future expiry date.');
                    }
                },
            ],
            'demo_card_cvc' => ['required', 'regex:/^\d{3}$/'],
            'items_json' => ['required', 'string', 'max:20000'],
        ]);

        $items = json_decode($data['items_json'], true);
        if (! is_array($items)) {
            throw ValidationException::withMessages([
                'items_json' => 'Your shopping bag could not be read. Please refresh and try again.',
            ]);
        }

        $validatedItems = Validator::make(['items' => $items], [
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.id' => ['required', 'integer', 'exists:products,id'],
            'items.*.opt' => ['required', 'string', 'max:80'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:10'],
        ])->validate()['items'];

        $order = DB::transaction(function () use ($data, $validatedItems, $request): Order {
            $lines = [];
            $productQuantities = [];
            $subtotal = 0;

            foreach ($validatedItems as $item) {
                $product = Product::query()
                    ->where('is_active', true)
                    ->whereHas('category', fn ($query) => $query->where('is_active', true))
                    ->lockForUpdate()
                    ->findOrFail($item['id']);

                if (! in_array($item['opt'], $product->options ?: ['One size'], true)) {
                    throw ValidationException::withMessages([
                        'items_json' => "The selected option for {$product->name} is invalid.",
                    ]);
                }

                $lineTotal = (float) $product->price * $item['qty'];
                $subtotal += $lineTotal;
                $lines[] = [$product, $item, $lineTotal];
                $productQuantities[$product->id] = ($productQuantities[$product->id] ?? 0) + $item['qty'];
            }

            foreach ($productQuantities as $productId => $quantity) {
                $product = Product::query()->lockForUpdate()->findOrFail($productId);
                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items_json' => "{$product->name} does not have enough stock for this order.",
                    ]);
                }
            }

            $shipping = $subtotal >= 80 ? 0 : 8;
            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_number' => 'BB-'.Str::upper(Str::random(10)),
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'demo_paid',
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'phone' => $data['phone'],
                'address_line' => $data['address_line'],
                'city' => $data['city'],
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'],
                'country' => $data['country'],
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'total' => $subtotal + $shipping,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lines as [$product, $item, $lineTotal]) {
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_option' => $item['opt'],
                    'quantity' => $item['qty'],
                    'unit_price' => $product->price,
                    'line_total' => $lineTotal,
                ]);
            }

            foreach ($productQuantities as $productId => $quantity) {
                Product::query()->whereKey($productId)->decrement('stock', $quantity);
            }

            return $order;
        });

        return redirect()->route('checkout.complete', $order);
    }

    public function complete(Order $order): View
    {
        abort_unless($order->user_id === request()->user()->id, 404);

        return view('checkout.complete', compact('order'));
    }
}
