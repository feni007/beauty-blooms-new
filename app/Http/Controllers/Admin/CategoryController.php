<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')->orderBy('name')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $category = new Category(['is_active' => true]);

        return view('admin.categories.form', compact('category'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? '') ?: $data['name']);
        $data['image_path'] = $request->file('image')?->store('categories', 'public');

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request, $category);
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? '') ?: $data['name'], $category);
        $newImage = $request->file('image')?->store('categories', 'public');
        if ($newImage) {
            $oldImage = $category->image_path;
            $data['image_path'] = $newImage;
            $category->update($data);
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
        } else {
            $category->update($data);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->withErrors([
                'category' => 'Move or delete this category’s products before deleting the category.',
            ]);
        }

        $image = $category->image_path;
        $category->delete();
        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('categories', 'slug')->ignore($category)],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'image.max' => 'Category image is too large. Please choose an image under 3 MB.',
            'image.image' => 'The category image must be a valid image file.',
            'image.mimes' => 'Category images must be JPG, PNG, or WebP.',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        unset($data['image']);

        return $data;
    }

    private function uniqueSlug(string $value, ?Category $category = null): string
    {
        $base = Str::slug($value) ?: 'category';
        $slug = $base;
        $suffix = 2;

        while (Category::where('slug', $slug)->when($category, fn ($query) => $query->where('id', '!=', $category->id))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
