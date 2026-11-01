<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class StorefrontController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->get();

        $storeCategories = $categories->map(fn (Category $category): array => [
            'name' => $category->name,
            'desc' => $category->description ?? '',
            'img' => $category->image_path
                ? Storage::disk('public')->url($category->image_path)
                : asset('images/product-placeholder.svg'),
        ])->values();

        $storeProducts = $products->map(fn (Product $product): array => [
            'id' => $product->id,
            'name' => $product->name,
            'cat' => $product->category->name,
            'price' => (float) $product->price,
            'rating' => (float) $product->rating,
            'reviews' => $product->reviews_count,
            'tint' => $product->tint,
            'optLabel' => $product->option_label,
            'tag' => $product->tag,
            'img' => $product->image_path
                ? Storage::disk('public')->url($product->image_path)
                : asset('images/product-placeholder.svg'),
            'opts' => $product->options ?: ['One size'],
            'swatches' => $product->swatches,
            'short' => $product->short_description ?? '',
            'desc' => $product->description ?? '',
            'benefits' => $product->benefits ?: [],
            'ingredients' => $product->ingredients ?? '',
            'how' => $product->how_to_use ?? '',
            'featured' => $product->is_featured,
            'stock' => $product->stock,
        ])->values();

        return view('storefront', compact('storeCategories', 'storeProducts'));
    }
}
