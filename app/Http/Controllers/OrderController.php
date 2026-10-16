<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function checkout()
    {
        return view('checkout', [
            'products' => Product::where('is_active', true)->get(['id', 'name', 'price', 'image']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'address' => ['required', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $order = DB::transaction(function () use ($data) {
            $requested = collect($data['items'])->keyBy('id');
            $products = Product::whereIn('id', $requested->keys())
                ->where('is_active', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== $requested->count()) {
                throw ValidationException::withMessages([
                    'items' => 'One or more products are no longer available.',
                ]);
            }

            $lines = $products->map(function (Product $product) use ($requested) {
                $quantity = (int) $requested[$product->id]['quantity'];
                $unitPrice = (float) $product->price;

                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'line_total' => round($unitPrice * $quantity, 2),
                ];
            });

            $subtotal = round($lines->sum('line_total'), 2);
            $shipping = $subtotal >= 50 ? 0 : 5.95;
            $order = Order::create([
                'order_number' => 'FE-' . Str::upper(Str::random(10)),
                'customer_name' => $data['customer_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => round($subtotal + $shipping, 2),
                'status' => 'received',
            ]);

            $order->items()->createMany($lines->values()->all());

            return $order;
        });

        return redirect()->route('checkout', ['order' => $order->order_number])
            ->with('success', "Order {$order->order_number} received. The store will contact you to confirm payment.");
    }
}
