<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', app()->environment('local') ? 'admin@beauty.test' : null);
        $adminPassword = env('ADMIN_PASSWORD', app()->environment('local') ? 'ChangeMe123!' : null);

        if (! is_string($adminEmail) || trim($adminEmail) === '' || ! is_string($adminPassword) || strlen($adminPassword) < 12) {
            throw new \RuntimeException('Set ADMIN_EMAIL and an ADMIN_PASSWORD of at least 12 characters before seeding an admin outside local development.');
        }

        User::updateOrCreate(
            ['email' => $adminEmail],
            ['name' => 'Beauty & Blooms Admin', 'password' => Hash::make($adminPassword), 'role' => 'admin'],
        );

        $categories = [
            ['name' => 'Skincare', 'description' => 'Serums, moisturisers and eye care built around botanical actives.'],
            ['name' => 'Makeup', 'description' => 'Lip, cheek and complexion in considered, wearable shades.'],
            ['name' => 'Fragrance', 'description' => 'Eaux de parfum and mists with a clear, lasting character.'],
            ['name' => 'Hair', 'description' => 'Oils and treatments for shine, softness and control.'],
            ['name' => 'Body', 'description' => 'Butters and rituals for softness from head to toe.'],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::updateOrCreate(
                ['slug' => str($categoryData['name'])->slug()->toString()],
                [...$categoryData, 'is_active' => true],
            );
            $categoryIds[$category->name] = $category->id;
        }

        $products = [
            ['name' => 'Rose Dew Hydrating Serum', 'category' => 'Skincare', 'price' => 48, 'stock' => 40, 'rating' => 4.9, 'reviews_count' => 812, 'tag' => 'Bestseller', 'tint' => '#efe4dc', 'short_description' => 'A weightless serum with rose-infused hydration for a soft, dewy finish.', 'description' => 'Damask rose water meets hyaluronic acid and squalane for plump, calm, luminous skin.', 'options' => ['30ml', '50ml'], 'benefits' => ['Hydrates for up to 24 hours', 'Softens the look of fine lines', 'Calms sensitive skin', 'Natural dewy finish'], 'ingredients' => 'Rose water, hyaluronic acid, squalane, niacinamide and glycerin.', 'how_to_use' => 'Apply three to four drops to clean skin morning and night.', 'is_featured' => true],
            ['name' => 'Velvet Cloud Moisturiser', 'category' => 'Skincare', 'price' => 56, 'stock' => 32, 'rating' => 4.8, 'reviews_count' => 604, 'tint' => '#f1e8df', 'short_description' => 'A cloud-light cream with ceramides that restores comfort without weight.', 'description' => 'Whipped shea butter and ceramides help restore the moisture barrier while keeping skin soft and smooth.', 'options' => ['50ml', '100ml'], 'benefits' => ['Restores the skin barrier', 'Lightweight texture', 'Suitable for all skin types'], 'ingredients' => 'Shea butter, ceramide NP, panthenol and allantoin.', 'how_to_use' => 'Press a pea-sized amount onto face and neck after serum.', 'is_featured' => true],
            ['name' => 'Vitamin C Glow Serum', 'category' => 'Skincare', 'price' => 52, 'stock' => 28, 'rating' => 4.8, 'reviews_count' => 477, 'tag' => 'New', 'tint' => '#f6e6c9', 'short_description' => 'Stabilised vitamin C with bright citrus botanicals for an even, radiant tone.', 'description' => 'A brightening serum with a stable vitamin C complex and ferulic acid.', 'options' => ['30ml'], 'benefits' => ['Brightens uneven tone', 'Antioxidant protection', 'Pairs well with SPF'], 'ingredients' => 'Vitamin C, ferulic acid, citrus botanicals and glycerin.', 'how_to_use' => 'Apply three drops each morning before moisturiser and SPF.', 'is_featured' => true],
            ['name' => 'Matte Rose Lipstick', 'category' => 'Makeup', 'price' => 28, 'stock' => 50, 'rating' => 4.8, 'reviews_count' => 1043, 'tint' => '#ead8d4', 'short_description' => 'A comfortable matte lipstick in a timeless rose shade.', 'description' => 'A rich, buildable colour with a soft matte finish and comfortable wear.', 'options' => ['Rose', 'Dusty Rose', 'Berry'], 'swatches' => ['#a65d65', '#bd777b', '#813e4f'], 'benefits' => ['Buildable colour', 'Comfortable matte finish', 'Cruelty-free'], 'ingredients' => 'Castor seed oil, candelilla wax, vitamin E and mineral pigments.', 'how_to_use' => 'Apply from the centre of the lips and blend outwards.', 'is_featured' => false],
            ['name' => 'Noir Rose Eau de Parfum', 'category' => 'Fragrance', 'price' => 88, 'stock' => 20, 'rating' => 4.9, 'reviews_count' => 956, 'tag' => 'Bestseller', 'tint' => '#e9dcd8', 'short_description' => 'Dark Bulgarian rose, softened with amber, vanilla and a whisper of oud.', 'description' => 'Blackcurrant and pink pepper open onto a velvet rose heart and a warm amber-sandalwood base.', 'options' => ['50ml', '100ml'], 'benefits' => ['Rose, oud and amber notes', 'Long-lasting character', 'Gift-ready packaging'], 'ingredients' => 'Alcohol denat., fragrance, water, linalool and limonene.', 'how_to_use' => 'Spray on pulse points from about 20cm away.', 'is_featured' => true],
            ['name' => 'Jasmine Dusk Perfume', 'category' => 'Fragrance', 'price' => 75, 'stock' => 22, 'rating' => 4.7, 'reviews_count' => 412, 'tint' => '#eee3d6', 'short_description' => 'Night-blooming jasmine and warm musk in a luminous floral trail.', 'description' => 'Sambac jasmine pairs with tonka bean and soft musk for an enveloping evening scent.', 'options' => ['75ml'], 'benefits' => ['Jasmine, tonka and musk', 'Warm and romantic', 'Refillable flacon'], 'ingredients' => 'Alcohol denat., fragrance, jasmine, tonka and musk.', 'how_to_use' => 'Apply to pulse points or mist lightly into the air.', 'is_featured' => false],
            ['name' => 'Cedar Night Eau de Toilette', 'category' => 'Fragrance', 'price' => 70, 'stock' => 16, 'rating' => 4.6, 'reviews_count' => 298, 'tag' => 'New', 'tint' => '#e6dfd6', 'short_description' => 'Smoked cedar, bergamot and soft vetiver for quiet evenings.', 'description' => 'A woody, airy fragrance with bergamot, cedar and mineral vetiver.', 'options' => ['50ml'], 'benefits' => ['Fresh woody character', 'Versatile daily wear', 'Recyclable packaging'], 'ingredients' => 'Alcohol denat., water, fragrance, bergamot and cedar.', 'how_to_use' => 'Spray lightly onto pulse points and clothing.', 'is_featured' => false],
            ['name' => 'Argan Gloss Hair Oil', 'category' => 'Hair', 'price' => 32, 'stock' => 36, 'rating' => 4.7, 'reviews_count' => 366, 'tint' => '#f1e3cf', 'short_description' => 'Cold-pressed argan oil that smooths frizz and adds a high-gloss finish.', 'description' => 'A featherlight oil that smooths the cuticle and adds shine without weighing hair down.', 'options' => ['100ml'], 'benefits' => ['Smooths frizz', 'High-gloss shine', 'Non-greasy finish'], 'ingredients' => 'Argan oil, jojoba oil and tocopherol.', 'how_to_use' => 'Warm two or three drops between palms and smooth through hair ends.', 'is_featured' => false],
            ['name' => 'Silk Repair Conditioner', 'category' => 'Hair', 'price' => 26, 'stock' => 30, 'rating' => 4.6, 'reviews_count' => 289, 'tint' => '#f3e7d9', 'short_description' => 'A rich conditioner that detangles and strengthens over time.', 'description' => 'Rose hip oil, keratin and silk protein smooth the cuticle for softer hair.', 'options' => ['250ml'], 'benefits' => ['Detangles without weight', 'Strengthens with keratin', 'Sulphate-free'], 'ingredients' => 'Rose hip oil, hydrolyzed silk, keratin and panthenol.', 'how_to_use' => 'Apply from mid-lengths to ends after shampooing, leave two minutes, then rinse.', 'is_featured' => false],
            ['name' => 'Honey Almond Body Butter', 'category' => 'Body', 'price' => 34, 'stock' => 24, 'rating' => 4.8, 'reviews_count' => 645, 'tint' => '#f3e1d6', 'short_description' => 'Whipped shea and wild honey that melts into skin for lasting softness.', 'description' => 'A rich but airy body butter with wild honey and sweet almond oil.', 'options' => ['200ml'], 'benefits' => ['Long-lasting moisture', 'Silky finish', 'Ideal for dry skin'], 'ingredients' => 'Shea butter, sweet almond oil, honey and glycerin.', 'how_to_use' => 'Apply generously after showering.', 'is_featured' => true],
            ['name' => 'Lavender Cloud Body Wash', 'category' => 'Body', 'price' => 22, 'stock' => 26, 'rating' => 4.7, 'reviews_count' => 352, 'tag' => 'New', 'tint' => '#e9e1ec', 'short_description' => 'A creamy, lavender-scented wash that cleanses without drying.', 'description' => 'Gentle cleansers, lavender and oat extract leave skin clean, calm and soft.', 'options' => ['250ml'], 'benefits' => ['Gentle daily cleanse', 'Calming lavender scent', 'Suitable for sensitive skin'], 'ingredients' => 'Oat extract, lavender oil, glycerin and mild cleansers.', 'how_to_use' => 'Lather over damp skin and rinse thoroughly.', 'is_featured' => false],
        ];

        foreach ($products as $productData) {
            $categoryName = $productData['category'];
            unset($productData['category']);
            Product::updateOrCreate(
                ['slug' => str($productData['name'])->slug()->toString()],
                [...$productData, 'category_id' => $categoryIds[$categoryName], 'is_active' => true, 'option_label' => 'Size'],
            );
        }
    }
}
