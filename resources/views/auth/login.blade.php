@extends('layouts.guest')

@section('page-title', __("Login"))

@section('content')
    <section class="layout-authentication">
        <div>
            <h1>@lang('Login with email address')</h1>
            <p>@lang('Here you can login using your email account')</p>

            <x-validation-errors/>

            @include('components.auth.login-form')
        </div>
    </section>
@endsection
