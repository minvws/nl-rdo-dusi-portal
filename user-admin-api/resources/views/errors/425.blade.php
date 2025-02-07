@extends('layouts.guest')

@section('page-title', __("Login currently not possible"))

@section('content')
    <section class="layout-authentication">
        <div>
            <div class="warning">
                <h1>@lang("Dear user")</h1>

                <p>@lang("Access denied: Your organisation has changed since your last login. Please contact the :app_name helpdesk, :email or :phone for more information.", [ 'app_name' => config('app.name'), 'email' => config('helpdesk.email'), 'phone' => config('helpdesk.phone') ])</p>
            </div>
        </div>
    </section>
@endsection
