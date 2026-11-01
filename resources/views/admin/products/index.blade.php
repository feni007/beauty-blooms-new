@extends('layouts.portal', ['title' => 'Products | Admin'])

@section('content')
    <div class="portal-row" style="margin-bottom:22px">
        <div><p class="kicker">Catalog</p><h1 class="portal-title" style="margin-bottom:0">Products.</h1></div>
        <a class="portal-button" href="{{ route('admin.products.create') }}">Add product</a>
    </div>
    <section class="portal-card portal-table-wrap">
        <table class="portal-table">
            <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Visibility</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td><img src="{{ $product->image_path ? Storage::disk('public')->url($product->image_path) : asset('images/product-placeholder.svg') }}" alt="{{ $product->name }} image"></td>
                        <td>{{ $product->name }}</td><td>{{ $product->category->name }}</td><td>${{ number_format((float) $product->price, 2) }}</td><td>{{ $product->stock }}</td>
                        <td><span class="portal-badge">{{ $product->is_active ? 'Active' : 'Hidden' }}</span></td>
                        <td><div class="portal-actions"><a class="portal-link" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                            <form method="post" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">@csrf @method('delete')<button class="portal-link" type="submit">Delete</button></form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="portal-muted">No products yet. Add a product or run the seeder.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:20px">{{ $products->links() }}</div>
    </section>
@endsection
