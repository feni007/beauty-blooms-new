@extends('layouts.portal', ['title' => ($category->exists ? 'Edit category' : 'Add category').' | Admin'])

@section('content')
    <div class="portal-row" style="margin-bottom:22px">
        <div><p class="kicker">Catalog</p><h1 class="portal-title" style="margin-bottom:0">{{ $category->exists ? 'Edit category.' : 'New category.' }}</h1></div>
        <a class="portal-link" href="{{ route('admin.categories.index') }}">← Back to categories</a>
    </div>
    <section class="portal-card">
        <form class="portal-form" method="post" enctype="multipart/form-data" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
            @csrf
            @if ($category->exists) @method('put') @endif
            <div class="portal-grid">
                <div class="portal-field"><label for="name">Category name *</label><input id="name" name="name" value="{{ old('name', $category->name) }}" required maxlength="100"></div>
                <div class="portal-field"><label for="slug">Slug (optional)</label><input id="slug" name="slug" value="{{ old('slug', $category->slug) }}" maxlength="120" placeholder="Created from category name"></div>
                <div class="portal-field full"><label for="description">Description</label><textarea id="description" name="description" maxlength="1000">{{ old('description', $category->description) }}</textarea></div>
                <div class="portal-field full">
                    <label for="image">Category image (optional, JPG/PNG/WebP; max 3 MB)</label>
                    <p class="portal-muted">Leave this empty to use the local placeholder image.</p>
                    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    @if ($category->image_path)
                        <img class="portal-image-preview" src="{{ Storage::disk('public')->url($category->image_path) }}" alt="Current category image">
                    @endif
                </div>
                <label class="portal-inline"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))> Visible in storefront</label>
            </div>
            <div class="portal-actions"><button class="portal-button" type="submit">Save category</button><a class="portal-button secondary" href="{{ route('admin.categories.index') }}">Cancel</a></div>
        </form>
    </section>
    <script>
        (() => {
            const form = document.querySelector('.portal-card form');
            const image = document.getElementById('image');
            const maxBytes = 3 * 1024 * 1024;
            image.addEventListener('change', () => {
                const file = image.files?.[0];
                if (file && file.size > maxBytes) {
                    image.value = '';
                    window.showPortalToast('Category image is too large. Please choose an image under 3 MB.', 'error');
                }
            });
            form.addEventListener('submit', (event) => {
                const file = image.files?.[0];
                if (file && file.size > maxBytes) {
                    event.preventDefault();
                    image.value = '';
                    window.showPortalToast('Category image is too large. Please choose an image under 3 MB.', 'error');
                }
            });
        })();
    </script>
@endsection
