@extends('layouts.portal', ['title' => ($product->exists ? 'Edit product' : 'Add product').' | Admin'])

@section('content')
    <div class="portal-row" style="margin-bottom:22px">
        <div><p class="kicker">Catalog</p><h1 class="portal-title" style="margin-bottom:0">{{ $product->exists ? 'Edit product.' : 'New product.' }}</h1></div>
        <a class="portal-link" href="{{ route('admin.products.index') }}">← Back to products</a>
    </div>
    <section class="portal-card">
        <form class="portal-form" method="post" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
            @csrf
            @if ($product->exists) @method('put') @endif
            <div class="portal-grid">
                <div class="portal-field"><label for="name">Product name *</label><input id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="160"></div>
                <div class="portal-field"><label for="category_id">Category *</label><select id="category_id" name="category_id" required><option value="">Choose a category</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}{{ $category->is_active ? '' : ' (hidden)' }}</option>@endforeach</select></div>
                <div class="portal-field"><label for="slug">Slug (optional)</label><input id="slug" name="slug" value="{{ old('slug', $product->slug) }}" maxlength="180" placeholder="Created from product name"></div>
                <div class="portal-field"><label for="sku">SKU</label><input id="sku" name="sku" value="{{ old('sku', $product->sku) }}" maxlength="80"></div>
                <div class="portal-field"><label for="price">Price *</label><input id="price" name="price" type="number" min=".01" step=".01" value="{{ old('price', $product->price) }}" required></div>
                <div class="portal-field"><label for="stock">Stock *</label><input id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', $product->stock ?? 0) }}" required></div>
                <div class="portal-field"><label for="rating">Rating (0–5)</label><input id="rating" name="rating" type="number" min="0" max="5" step=".1" value="{{ old('rating', $product->rating ?? 0) }}" required></div>
                <div class="portal-field"><label for="reviews_count">Review count</label><input id="reviews_count" name="reviews_count" type="number" min="0" value="{{ old('reviews_count', $product->reviews_count ?? 0) }}" required></div>
                <div class="portal-field"><label for="tag">Product tag</label><input id="tag" name="tag" value="{{ old('tag', $product->tag) }}" maxlength="40" placeholder="Bestseller, New, etc."></div>
                <div class="portal-field"><label for="tint">Card background color</label><input id="tint" name="tint" value="{{ old('tint', $product->tint ?? '#efe4dc') }}" pattern="#[0-9A-Fa-f]{6}" required></div>
                <div class="portal-field"><label for="option_label">Option label</label><input id="option_label" name="option_label" value="{{ old('option_label', $product->option_label ?? 'Size') }}" maxlength="40" required></div>
                <div class="portal-field full"><label for="options_text">Options (comma or line separated) *</label><textarea id="options_text" name="options_text" required>{{ old('options_text', implode("\n", $product->options ?? ['One size'])) }}</textarea></div>
                <div class="portal-field full"><label for="short_description">Short description</label><textarea id="short_description" name="short_description" maxlength="500">{{ old('short_description', $product->short_description) }}</textarea></div>
                <div class="portal-field full"><label for="description">Product description</label><textarea id="description" name="description" maxlength="10000">{{ old('description', $product->description) }}</textarea></div>
                <div class="portal-field full"><label for="benefits_text">Benefits (one per line)</label><textarea id="benefits_text" name="benefits_text">{{ old('benefits_text', implode("\n", $product->benefits ?? [])) }}</textarea></div>
                <div class="portal-field full"><label for="ingredients">Ingredients</label><textarea id="ingredients" name="ingredients">{{ old('ingredients', $product->ingredients) }}</textarea></div>
                <div class="portal-field full"><label for="how_to_use">How to use</label><textarea id="how_to_use" name="how_to_use">{{ old('how_to_use', $product->how_to_use) }}</textarea></div>
                <div class="portal-field full">
                    <label for="image">Product image (JPG, PNG or WebP; max 3 MB)</label>
                    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    <img
                        id="imagePreview"
                        class="portal-image-preview"
                        src="{{ $product->image_path ? Storage::disk('public')->url($product->image_path) : asset('images/product-placeholder.svg') }}"
                        alt="{{ $product->image_path ? 'Current product image' : 'Product image preview' }}"
                    >
                </div>
                <label class="portal-inline"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))> Show in featured products</label>
                <label class="portal-inline"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))> Visible in storefront</label>
            </div>
            <div class="portal-actions"><button class="portal-button" type="submit">Save product</button><a class="portal-button secondary" href="{{ route('admin.products.index') }}">Cancel</a></div>
        </form>
    </section>
    <script>
        (() => {
            const form = document.querySelector('.portal-card form');
            const image = document.getElementById('image');
            const preview = document.getElementById('imagePreview');
            const initialPreview = preview.src;
            const fallbackImage = @js(asset('images/product-placeholder.svg'));
            const maxBytes = 3 * 1024 * 1024;
            let previewUrl = null;

            preview.addEventListener('error', () => {
                if (preview.src !== fallbackImage) {
                    preview.src = fallbackImage;
                }
            });

            image.addEventListener('change', () => {
                const file = image.files?.[0];
                if (file && file.size > maxBytes) {
                    image.value = '';
                    preview.src = initialPreview;
                    window.showPortalToast('Product image is too large. Please choose an image under 3 MB.', 'error');
                    return;
                }
                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                    previewUrl = null;
                }
                if (file) {
                    previewUrl = URL.createObjectURL(file);
                    preview.src = previewUrl;
                } else {
                    preview.src = initialPreview;
                }
            });

            form.addEventListener('submit', (event) => {
                const file = image.files?.[0];
                if (file && file.size > maxBytes) {
                    event.preventDefault();
                    image.value = '';
                    preview.src = initialPreview;
                    window.showPortalToast('Product image is too large. Please choose an image under 3 MB.', 'error');
                }
            });

            window.addEventListener('beforeunload', () => {
                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                }
            });
        })();
    </script>
@endsection
