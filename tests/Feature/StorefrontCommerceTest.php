<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorefrontCommerceTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_uses_database_catalog_and_local_image_paths(): void
    {
        $category = Category::create([
            'name' => 'Skincare',
            'slug' => 'skincare',
            'description' => 'Botanical care',
            'is_active' => true,
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Garden Serum',
            'slug' => 'garden-serum',
            'price' => 48,
            'stock' => 5,
            'image_path' => 'products/garden-serum.webp',
            'options' => ['30ml'],
            'is_featured' => true,
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Garden Serum')
            ->assertSee('garden-serum.webp')
            ->assertSee('images/product-placeholder.svg')
            ->assertSee('class="loader"', false)
            ->assertSee('IntersectionObserver')
            ->assertSee('data-parallax')
            ->assertDontSee('images.unsplash.com');
    }

    public function test_checkout_requires_authentication(): void
    {
        $this->get(route('checkout.create'))
            ->assertRedirect(route('login'));

        $this->post(route('checkout.store'), [])
            ->assertRedirect(route('login'));
    }

    public function test_authentication_and_authenticated_checkout_pages_render(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Sign in to your account.');
        $this->get(route('register'))->assertOk()->assertSee('Create your account.');
        $this->get(route('password.request'))->assertOk()->assertSee('Reset your password.');
        $this->get(route('password.reset', ['token' => 'sample-token']))->assertOk()->assertSee('Choose a new password.');

        $this->actingAs(User::factory()->create())
            ->get(route('checkout.create'))
            ->assertOk()
            ->assertSee('Delivery details.')
            ->assertSee('itemsJson');
    }

    public function test_authenticated_checkout_creates_order_from_database_prices_and_decrements_inventory(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Skincare',
            'slug' => 'skincare',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Garden Serum',
            'slug' => 'garden-serum',
            'price' => 48,
            'stock' => 5,
            'options' => ['30ml', '50ml'],
            'is_active' => true,
        ]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Test Customer',
            'customer_email' => $user->email,
            'phone' => '555-0100',
            'address_line' => '1 Flower Street',
            'city' => 'Bloomfield',
            'state' => 'CA',
            'postal_code' => '90210',
            'country' => 'United States',
            ...$this->demoCardDetails(),
            'items_json' => json_encode([['id' => $product->id, 'opt' => '30ml', 'qty' => 1]]),
        ])->assertRedirect();

        $order = Order::with('items')->firstOrFail();
        $this->assertSame(56.0, (float) $order->total);
        $this->assertSame('pending', $order->status);
        $this->assertSame(1, $order->items->first()->quantity);
        $this->assertSame(4, $product->fresh()->stock);
    }

    public function test_demo_card_checkout_is_recorded_as_a_simulation_without_card_details(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Skincare', 'slug' => 'skincare', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Garden Serum',
            'slug' => 'garden-serum',
            'price' => 48,
            'stock' => 5,
            'options' => ['30ml'],
            'is_active' => true,
        ]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'phone' => '555-0100',
            'address_line' => '1 Flower Street',
            'city' => 'Bloomfield',
            'postal_code' => '90210',
            'country' => 'United States',
            ...$this->demoCardDetails(),
            'items_json' => json_encode([['id' => $product->id, 'opt' => '30ml', 'qty' => 1]]),
        ])->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame('demo_card', $order->payment_method);
        $this->assertSame('demo_paid', $order->payment_status);
        $this->assertFalse(Schema::hasColumn('orders', 'payment_card_number'));
        $this->assertFalse(Schema::hasColumn('orders', 'card_number'));
        $this->actingAs($user)
            ->get(route('checkout.complete', $order))
            ->assertOk()
            ->assertSee('Demo card payment simulated successfully (no real charge).');
    }

    public function test_checkout_requires_valid_demo_card_details_on_the_server(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Skincare', 'slug' => 'skincare', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Garden Serum',
            'slug' => 'garden-serum',
            'price' => 48,
            'stock' => 5,
            'options' => ['30ml'],
            'is_active' => true,
        ]);
        $checkout = [
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'phone' => '555-0100',
            'address_line' => '1 Flower Street',
            'city' => 'Bloomfield',
            'postal_code' => '90210',
            'country' => 'United States',
            'items_json' => json_encode([['id' => $product->id, 'opt' => '30ml', 'qty' => 1]]),
        ];

        $this->actingAs($user)->from(route('checkout.create'))
            ->post(route('checkout.store'), $checkout)
            ->assertSessionHasErrors(['payment_method', 'demo_cardholder', 'demo_card_number', 'demo_card_expiry', 'demo_card_cvc']);

        $this->post(route('checkout.store'), [
            ...$checkout,
            ...$this->demoCardDetails(),
            'demo_card_number' => '4111 1111 1111 1111',
        ])->assertSessionHasErrors('demo_card_number');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_checkout_displays_the_animated_live_card_preview_with_required_card_fields(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('checkout.create'))
            ->assertOk()
            ->assertSee('card-preview', false)
            ->assertSee('class="card-inner"', false)
            ->assertSee('id="cardDisplay"', false)
            ->assertSee('id="nameDisplay"', false)
            ->assertSee('id="expDisplay"', false)
            ->assertSee('cardNumber.addEventListener', false)
            ->assertSee('name="demo_card_number"', false)
            ->assertSee('name="demo_card_cvc"', false)
            ->assertSee('required');

        $this->assertStringContainsString(
            'rotateY(-5deg) rotateX(3deg)',
            file_get_contents(public_path('css/storefront.css')),
        );
    }

    public function test_checkout_rejects_unavailable_options_without_changing_stock(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Body', 'slug' => 'body', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Body Cream',
            'slug' => 'body-cream',
            'price' => 34,
            'stock' => 4,
            'options' => ['200ml'],
            'is_active' => true,
        ]);

        $this->actingAs($user)->from(route('checkout.create'))->post(route('checkout.store'), [
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'phone' => '555-0100',
            'address_line' => '1 Flower Street',
            'city' => 'Bloomfield',
            'postal_code' => '90210',
            'country' => 'United States',
            ...$this->demoCardDetails(),
            'items_json' => json_encode([['id' => $product->id, 'opt' => '1 litre', 'qty' => 1]]),
        ])->assertSessionHasErrors('items_json');

        $this->assertSame(4, $product->fresh()->stock);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_admin_routes_are_not_available_to_customer_accounts(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_can_open_dashboard_and_catalog_forms(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Store management');

        $this->get(route('admin.products.create'))->assertOk()->assertSee('New product.');
        $this->get(route('admin.categories.create'))
            ->assertOk()
            ->assertSee('New category.')
            ->assertSee('Category image (optional')
            ->assertSee('type="file"', false)
            ->assertSee('enctype="multipart/form-data"', false);
    }

    public function test_admin_product_image_is_saved_to_public_storage(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Skincare', 'slug' => 'skincare', 'is_active' => true]);

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Garden Cream',
            'price' => '35.00',
            'stock' => 8,
            'tint' => '#efe4dc',
            'option_label' => 'Size',
            'options_text' => "50ml\n100ml",
            'rating' => 4.5,
            'reviews_count' => 12,
            'is_active' => 1,
            'image' => UploadedFile::fake()->image('garden-cream.jpg', 500, 600),
        ])->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Garden Cream')->firstOrFail();
        $this->assertStringStartsWith('products/', $product->image_path);
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_admin_can_create_category_without_image_upload_and_sees_success_toast(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Botanical Care',
            'description' => 'Plant-led formulas.',
            'is_active' => '1',
        ])->assertRedirect(route('admin.categories.index'));

        $category = Category::where('name', 'Botanical Care')->firstOrFail();
        $this->assertTrue(Schema::hasColumn('categories', 'image_path'));
        $this->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Category created.')
            ->assertSee('portalToast', false);
    }

    public function test_admin_can_upload_category_image_and_storefront_uses_it(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Botanical Care',
            'description' => 'Plant-led formulas.',
            'is_active' => '1',
            'image' => UploadedFile::fake()->image('botanical-care.jpg', 500, 600),
        ])->assertRedirect(route('admin.categories.index'));

        $category = Category::where('name', 'Botanical Care')->firstOrFail();
        $this->assertStringStartsWith('categories/', $category->image_path);
        Storage::disk('public')->assertExists($category->image_path);

        $this->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee(basename($category->image_path));
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(basename($category->image_path));
    }

    public function test_oversized_category_image_shows_clear_toast_and_is_not_saved(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('admin.categories.create'))
            ->post(route('admin.categories.store'), [
                'name' => 'Botanical Care',
                'is_active' => '1',
                'image' => UploadedFile::fake()->image('too-large.jpg')->size(4096),
            ])
            ->assertRedirect(route('admin.categories.create'))
            ->assertSessionHasErrors('image');

        $this->assertDatabaseMissing('categories', ['name' => 'Botanical Care']);
        $this->get(route('admin.categories.create'))
            ->assertOk()
            ->assertSee('Category image is too large. Please choose an image under 3 MB.')
            ->assertSee('portal-toast-error', false);
    }

    public function test_category_action_error_is_shown_in_a_toast(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Skincare', 'slug' => 'skincare', 'is_active' => true]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Garden Serum',
            'slug' => 'garden-serum',
            'price' => 48,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.categories.index'))
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));

        $this->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Move or delete this category’s products before deleting the category.')
            ->assertSee('portal-toast-error', false);
    }

    public function test_admin_can_review_orders_and_cancelling_restores_inventory(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create();
        $category = Category::create(['name' => 'Body', 'slug' => 'body', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Body Cream',
            'slug' => 'body-cream',
            'price' => 34,
            'stock' => 3,
            'options' => ['200ml'],
            'is_active' => true,
        ]);
        $order = Order::create([
            'user_id' => $customer->id,
            'order_number' => 'BB-TEST00001',
            'status' => 'pending',
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'phone' => '555-0100',
            'address_line' => '1 Flower Street',
            'city' => 'Bloomfield',
            'postal_code' => '90210',
            'country' => 'United States',
            'subtotal' => 34,
            'shipping_cost' => 8,
            'total' => 42,
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_option' => '200ml',
            'quantity' => 2,
            'unit_price' => 34,
            'line_total' => 68,
        ]);
        $product->update(['stock' => 1]);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee($customer->email);

        $this->patch(route('admin.orders.update', $order), ['status' => 'cancelled'])
            ->assertRedirect();

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_customer_registration_cannot_assign_admin_role(): void
    {
        $this->post(route('register.store'), [
            'name' => 'New Customer',
            'email' => 'new-customer@example.test',
            'password' => 'GardenPassword123',
            'password_confirmation' => 'GardenPassword123',
            'role' => 'admin',
        ])->assertRedirect();

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'new-customer@example.test',
            'role' => 'customer',
        ]);
    }

    private function demoCardDetails(): array
    {
        return [
            'payment_method' => 'demo_card',
            'demo_cardholder' => 'Test Customer',
            'demo_card_number' => '4242 4242 4242 4242',
            'demo_card_expiry' => now()->addYear()->format('m/y'),
            'demo_card_cvc' => '123',
        ];
    }
}
