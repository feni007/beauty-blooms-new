<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Beauty & Blooms' }}</title>
    <link rel="stylesheet" href="{{ asset('css/storefront.css') }}">
</head>
<body class="portal">
    <header class="portal-header">
        <div class="container portal-header-in">
            <a href="{{ route('home') }}" class="logo">Beauty <em>&amp;</em> Blooms</a>
            <nav class="portal-nav">
                <a href="{{ route('home') }}">Store</a>
                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                        <a href="{{ route('admin.products.index') }}">Products</a>
                        <a href="{{ route('admin.categories.index') }}">Categories</a>
                        <a href="{{ route('admin.orders.index') }}">Orders</a>
                    @else
                        <a href="{{ route('orders.index') }}">My orders</a>
                    @endif
                    <form method="post" action="{{ route('logout') }}">@csrf
                        <button class="portal-link" type="submit">Sign out</button>
                    </form>
                @endauth
            </nav>
        </div>
    </header>
    <main class="portal-main">
        @php
            $toastMessage = session('success') ?? session('status') ?? ($errors->any() ? $errors->first() : null);
            $toastIsError = $errors->any() && !session('success') && !session('status');
        @endphp
        @if ($errors->any())
            <div class="portal-errors" role="alert">
                <strong>Please check the following:</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
    @if ($toastMessage)
        <div id="portalToast" class="toast portal-toast {{ $toastIsError ? 'portal-toast-error' : '' }}" role="{{ $toastIsError ? 'alert' : 'status' }}" aria-live="{{ $toastIsError ? 'assertive' : 'polite' }}">
            <i aria-hidden="true">{{ $toastIsError ? '!' : '✓' }}</i>
            <span>{{ $toastMessage }}</span>
        </div>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const toast = document.getElementById('portalToast');
                requestAnimationFrame(() => toast.classList.add('show'));
                window.setTimeout(() => toast.classList.remove('show'), 4200);
            });
        </script>
    @endif
    <script>
        window.showPortalToast = (message, type = 'success') => {
            const toast = document.getElementById('portalToast') || document.createElement('div');
            toast.id = 'portalToast';
            toast.className = `toast portal-toast ${type === 'error' ? 'portal-toast-error' : ''}`;
            toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
            toast.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
            toast.replaceChildren();
            const icon = document.createElement('i');
            icon.setAttribute('aria-hidden', 'true');
            icon.textContent = type === 'error' ? '!' : '✓';
            const text = document.createElement('span');
            text.textContent = message;
            toast.append(icon, text);
            if (!toast.isConnected) document.body.append(toast);
            requestAnimationFrame(() => toast.classList.add('show'));
            window.setTimeout(() => toast.classList.remove('show'), 4200);
        };
    </script>
</body>
</html>
