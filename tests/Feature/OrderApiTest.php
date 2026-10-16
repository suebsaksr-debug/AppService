<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_token_can_list_all_orders_with_their_items(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('order-test')->plainTextToken;
        $order = $this->createOrder('FE-LISTED');

        $this->withToken($token)
            ->getJson('/api/orders')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $order->id)
            ->assertJsonPath('0.items.0.product_name', 'Test product');
    }

    public function test_api_token_can_show_an_order_by_id_with_its_items(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('order-test')->plainTextToken;
        $order = $this->createOrder('FE-SHOW');

        $this->withToken($token)
            ->getJson('/api/orders/'.$order->id)
            ->assertOk()
            ->assertJsonPath('id', $order->id)
            ->assertJsonPath('items.0.product_name', 'Test product');
    }

    public function test_order_api_requires_a_bearer_token(): void
    {
        $this->getJson('/api/orders')->assertUnauthorized();
        $this->getJson('/api/orders/1')->assertUnauthorized();
    }

    public function test_order_api_returns_not_found_for_an_unknown_id(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('order-test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/orders/999')
            ->assertNotFound();
    }

    private function createOrder(string $orderNumber): Order
    {
        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '123456789',
            'address' => '123 Test Street',
            'subtotal' => '10.00',
            'shipping' => '0.00',
            'total' => '10.00',
            'status' => 'received',
        ]);

        $order->items()->save(new OrderItem([
            'product_name' => 'Test product',
            'unit_price' => '10.00',
            'quantity' => 1,
            'line_total' => '10.00',
        ]));

        return $order;
    }
}
