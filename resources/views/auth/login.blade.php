@extends('layouts.portal', ['title' => 'Sign in | Beauty & Blooms'])

@section('content')
    <section class="portal-card" style="max-width:560px;margin:0 auto">
        <p class="kicker">Welcome back</p>
        <h1 class="portal-title">Sign in to your account.</h1>
        <form class="portal-form" method="post" action="{{ route('login.store') }}">
            @csrf
            <div class="portal-field">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            </div>
            <div class="portal-field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <a class="portal-link" href="{{ route('password.request') }}">Forgot your password?</a>
            <label class="portal-inline"><input type="checkbox" name="remember" value="1"> Remember me</label>
            <button class="portal-button" type="submit">Sign in</button>
        </form>
        <p class="portal-muted" style="margin-top:22px">New to Beauty &amp; Blooms? <a class="portal-link" href="{{ route('register') }}">Create an account</a>.</p>
    </section>
@endsection
