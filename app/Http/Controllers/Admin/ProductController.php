<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')->latest()->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $product = new Product([
            'is_active' => true,
            'options' => ['One size'],
            'benefits' => [],
        ]);
        $categories = Category::orderBy('name')->get();

        return view('admin.products.form', compact('product', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? '') ?: $data['name']);
        $data['image_path'] = $request->file('image')?->store('products', 'public');
        unset($data['image']);

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product);
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? '') ?: $data['name'], $product);
        unset($data['image']);

        $newImage = $request->file('image')?->store('products', 'public');
        if ($newImage) {
            $oldImage = $product->image_path;
            $data['image_path'] = $newImage;
            $product->update($data);
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
        } else {
            $product->update($data);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $image = $product->image_path;
        $product->delete();
        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('products', 'slug')->ignore($product)],
            'sku' => ['nullable', 'string', 'max:80', Rule::unique('products', 'sku')->ignore($product)],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'tint' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'tag' => ['nullable', 'string', 'max:40'],
            'option_label' => ['required', 'string', 'max:40'],
            'options_text' => ['required', 'string', 'max:500'],
            'benefits_text' => ['nullable', 'string', 'max:2000'],
            'ingredients' => ['nullable', 'string', 'max:3000'],
            'how_to_use' => ['nullable', 'string', 'max:3000'],
            'rating' => ['required', 'numeric', 'min:0', 'max:5'],
            'reviews_count' => ['required', 'integer', 'min:0', 'max:1000000'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['options'] = collect(preg_split('/[\r\n,]+/', $data['options_text']))
            ->map(fn (string $option): string => trim($option))
            ->filter()
            ->unique()
            ->values()
            ->all();
        $data['benefits'] = collect(preg_split('/[\r\n]+/', $data['benefits_text'] ?? ''))
            ->map(fn (string $benefit): string => trim($benefit))
            ->filter()
            ->values()
            ->all();
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');
        unset($data['options_text'], $data['benefits_text']);

        if ($data['options'] === []) {
            throw ValidationException::withMessages([
                'options_text' => 'Enter at least one product option.',
            ]);
        }

        return $data;
    }

    private function uniqueSlug(string $value, ?Product $product = null): string
    {
        $base = Str::slug($value) ?: 'product';
        $slug = $base;
        $suffix = 2;

        while (Product::where('slug', $slug)->when($product, fn ($query) => $query->where('id', '!=', $product->id))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
