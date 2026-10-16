<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_the_backend_master_and_see_database_products(): void
    {
        $this->actingAs(User::factory()->create());
        Product::create([
            'name' => 'Database Product',
            'category' => 'Tech',
            'price' => '12.50',
            'is_active' => true,
        ]);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewIs('template.backend.master')
            ->assertSee('Database Product')
            ->assertSee('$12.50');
    }

    public function test_authenticated_user_can_return_to_dashboard_from_store_homepage(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Back to dashboard')
            ->assertSee(route('admin.dashboard'));
    }

    public function test_authenticated_user_can_create_update_and_delete_products(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.products.store'), [
            'name' => 'Canvas Tote',
            'category' => 'Bags',
            'description' => 'Everyday tote',
            'price' => '32.00',
            'image' => 'images/categories/bags.jpg',
            'is_active' => '1',
        ])->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Canvas Tote')->firstOrFail();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'is_active' => true]);
        $this->get(route('shop'))->assertOk()->assertSee('Canvas Tote');

        $this->put(route('admin.products.update', $product), [
            'name' => 'Updated Canvas Tote',
            'category' => 'Bags',
            'description' => 'Updated tote',
            'price' => '35.00',
            'image' => 'images/categories/bags.jpg',
            'is_active' => '1',
        ])->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Canvas Tote']);
        $this->get(route('shop'))->assertSee('Updated Canvas Tote');

        $this->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->get(route('shop'))->assertDontSee('Updated Canvas Tote');
    }

    public function test_api_token_can_create_a_product(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('product-test')->plainTextToken;

        $this->withToken($token)->postJson('/api/products', [
            'name' => 'API Backpack',
            'category' => 'Bags',
            'description' => 'Lightweight backpack',
            'price' => 49.95,
            'image' => 'images/categories/bags.jpg',
            'is_active' => true,
        ])->assertCreated()
            ->assertJsonPath('message', 'เพิ่มสินค้าสำเร็จ')
            ->assertJsonPath('product.name', 'API Backpack');

        $this->assertDatabaseHas('products', [
            'name' => 'API Backpack',
            'category' => 'Bags',
        ]);
    }

    public function test_api_token_can_list_and_show_products(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('product-test')->plainTextToken;
        $product = Product::create([
            'name' => 'Listed Product',
            'category' => 'Tech',
            'price' => '12.50',
            'is_active' => true,
        ]);

        $this->withToken($token)
            ->getJson('/api/products')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Listed Product']);

        $this->withToken($token)
            ->getJson('/api/products/'.$product->id)
            ->assertOk()
            ->assertJsonPath('id', $product->id);
    }

    public function test_api_product_creation_requires_a_bearer_token(): void
    {
        $this->postJson('/api/products', [
            'name' => 'Unauthorized Product',
            'category' => 'Tech',
            'price' => 10,
            'is_active' => true,
        ])->assertUnauthorized();
    }

    public function test_api_token_can_update_and_delete_a_product(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('product-test')->plainTextToken;
        $product = Product::create([
            'name' => 'Old Product Name',
            'category' => 'Tech',
            'price' => '10.00',
            'is_active' => true,
        ]);

        $this->withToken($token)->putJson('/api/products/'.$product->id, [
            'name' => 'Updated Product Name',
            'category' => 'Bags',
            'description' => 'Updated description',
            'price' => 25.50,
            'image' => 'images/categories/bags.jpg',
            'is_active' => false,
        ])->assertOk()
            ->assertJsonPath('message', 'แก้ไขสินค้าสำเร็จ')
            ->assertJsonPath('product.name', 'Updated Product Name');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'is_active' => false,
        ]);

        $this->withToken($token)
            ->deleteJson('/api/products/'.$product->id)
            ->assertOk()
            ->assertJson(['message' => 'ลบสินค้าสำเร็จ']);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_api_token_can_update_a_product_using_form_method_override(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('product-test')->plainTextToken;
        $product = Product::create([
            'name' => 'Old Product Name',
            'category' => 'Tech',
            'price' => '10.00',
            'is_active' => true,
        ]);

        $this->withToken($token)->post('/api/products/'.$product->id, [
            '_method' => 'PUT',
            'name' => 'Updated From Form',
            'category' => 'Home',
            'description' => 'Form encoded update',
            'price' => '13000',
            'image' => 'https://example.com/product.jpg',
            'is_active' => '1',
        ])->assertOk()
            ->assertJsonPath('product.name', 'Updated From Form');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated From Form',
        ]);
    }

    public function test_api_product_update_and_delete_require_a_bearer_token(): void
    {
        $product = Product::create([
            'name' => 'Protected Product',
            'category' => 'Tech',
            'price' => '10.00',
            'is_active' => true,
        ]);

        $this->putJson('/api/products/'.$product->id, [
            'name' => 'Changed Product',
            'category' => 'Tech',
            'price' => 10,
            'is_active' => true,
        ])->assertUnauthorized();

        $this->deleteJson('/api/products/'.$product->id)->assertUnauthorized();
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_authenticated_user_without_admin_role_can_manage_products(): void
    {
        $product = Product::create([
            'name' => 'Customer Product',
            'category' => 'Tech',
            'price' => '10.00',
            'is_active' => true,
        ]);

        $this->actingAs(User::factory()->create(['is_admin' => false]));

        $this->get(route('admin.products.index'))->assertOk();
        $this->post(route('admin.products.store'), [
            'name' => 'Unauthorized Product',
            'category' => 'Tech',
            'price' => '10.00',
            'is_active' => '1',
        ])->assertRedirect(route('admin.products.index'));
        $this->put(route('admin.products.update', $product), [
            'name' => 'Changed Product',
            'category' => 'Tech',
            'price' => '10.00',
            'is_active' => '1',
        ])->assertRedirect(route('admin.products.index'));
        $this->delete(route('admin.products.destroy', $product))->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseHas('products', ['name' => 'Unauthorized Product']);
    }

    public function test_guests_are_redirected_to_login_before_managing_products(): void
    {
        $this->get(route('admin.products.index'))->assertRedirect(route('login'));
    }
}
