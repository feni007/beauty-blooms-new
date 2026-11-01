<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploaded_product_image_is_stored_and_available_in_admin_and_storefront(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create([
            'name' => 'Skincare',
            'slug' => 'skincare',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('imagePreview');

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'category_id' => $category->id,
                'name' => 'Bloom Serum',
                'price' => 12.50,
                'stock' => 5,
                'rating' => 4.5,
                'reviews_count' => 3,
                'tint' => '#efe4dc',
                'option_label' => 'Size',
                'options_text' => '30 ml',
                'image' => UploadedFile::fake()->createWithContent(
                    'bloom.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC')
                ),
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.products.index'));

        $product = Product::query()->where('name', 'Bloom Serum')->firstOrFail();
        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);

        $imageUrl = Storage::disk('public')->url($product->image_path);
        $this->get(parse_url($imageUrl, PHP_URL_PATH))
            ->assertOk()
            ->assertHeader('content-type', 'image/png');

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee($product->image_path);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(basename($product->image_path));
    }
}
