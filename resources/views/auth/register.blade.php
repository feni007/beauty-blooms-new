@extends('layouts.portal', ['title' => 'Create an account | Beauty & Blooms'])

@section('content')
    <section class="portal-card" style="max-width:620px;margin:0 auto">
        <p class="kicker">Join the garden</p>
        <h1 class="portal-title">Create your account.</h1>
        <form class="portal-form" method="post" action="{{ route('register.store') }}">
            @csrf
            <div class="portal-field">
                <label for="name">Full name</label>
                <input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>
            </div>
            <div class="portal-field">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
            </div>
            <div class="portal-field">
                <label for="password">Password (at least 10 characters)</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required>
            </div>
            <div class="portal-field">
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            </div>
            <button class="portal-button" type="submit">Create account</button>
        </form>
        <p class="portal-muted" style="margin-top:22px">Already registered? <a class="portal-link" href="{{ route('login') }}">Sign in</a>.</p>
    </section>
@endsection
