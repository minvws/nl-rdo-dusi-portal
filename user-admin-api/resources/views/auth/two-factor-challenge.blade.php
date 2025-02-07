@extends('layouts.guest')

@section('page-title', __('Login (2FA)'))

@section('content')
<section class="layout-authentication">
    <div>
        <h1>@lang('Login with email address')</h1>

        <p>
            @lang('Please confirm access to your account by entering the authentication code provided by your authenticator application.')
        </p>

        <x-validation-errors />

        <form
            action="{{ route('two-factor.login') }}"
            autocomplete="off"
            class="horizontal-view"
            method="POST"
        >
            @csrf

            <div>
                <label for="code">@lang('Code')</label>
                <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]*" required autofocus autocomplete="off" />
            </div>

            <x-button>@lang('Login')</x-button>
        </form>
    </div>
</section>
@endsection
