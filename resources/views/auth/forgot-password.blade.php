@extends('layouts.portal', ['title' => 'Reset password | Beauty & Blooms'])

@section('content')
    <section class="portal-card" style="max-width:560px;margin:0 auto">
        <p class="kicker">Account access</p>
        <h1 class="portal-title">Reset your password.</h1>
        @if (session('status'))<div class="portal-alert">{{ session('status') }}</div>@endif
        <p class="portal-muted" style="margin-bottom:20px">Enter the email address for your account and we’ll send a password reset link.</p>
        <form class="portal-form" method="post" action="{{ route('password.email') }}">
            @csrf
            <div class="portal-field"><label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus></div>
            <button class="portal-button" type="submit">Email reset link</button>
        </form>
    </section>
@endsection
