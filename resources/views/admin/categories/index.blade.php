@extends('layouts.portal', ['title' => 'Categories | Admin'])

@section('content')
    <div class="portal-row" style="margin-bottom:22px">
        <div><p class="kicker">Catalog</p><h1 class="portal-title" style="margin-bottom:0">Categories.</h1></div>
        <a class="portal-button" href="{{ route('admin.categories.create') }}">Add category</a>
    </div>
    <section class="portal-card portal-table-wrap">
        <table class="portal-table">
            <thead><tr><th>Image</th><th>Name</th><th>Slug</th><th>Products</th><th>Visibility</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td><img src="{{ $category->image_path ? Storage::disk('public')->url($category->image_path) : asset('images/product-placeholder.svg') }}" alt=""></td>
                        <td>{{ $category->name }}</td><td>{{ $category->slug }}</td><td>{{ $category->products_count }}</td>
                        <td><span class="portal-badge">{{ $category->is_active ? 'Active' : 'Hidden' }}</span></td>
                        <td><div class="portal-actions"><a class="portal-link" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                            <form method="post" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">@csrf @method('delete')<button class="portal-link" type="submit">Delete</button></form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="portal-muted">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:20px">{{ $categories->links() }}</div>
    </section>
@endsection
