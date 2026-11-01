@extends('layouts.portal', ['title' => 'Choose a new password | Beauty & Blooms'])

@section('content')
    <section class="portal-card" style="max-width:560px;margin:0 auto">
        <p class="kicker">Account access</p>
        <h1 class="portal-title">Choose a new password.</h1>
        <form class="portal-form" method="post" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="portal-field"><label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="email" required></div>
            <div class="portal-field"><label for="password">New password (at least 10 characters)</label><input id="password" name="password" type="password" autocomplete="new-password" required></div>
            <div class="portal-field"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required></div>
            <button class="portal-button" type="submit">Update password</button>
        </form>
    </section>
@endsection
