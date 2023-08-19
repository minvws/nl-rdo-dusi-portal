@extends('layouts.guest')

@section('page-title', __("Login currently not possible"))

@section('content')
    <section class="layout-authentication">
        <div>
            <div class="warning">
                <h1>@lang("Dear user")</h1>

                <p>@lang("Access denied: Your account has been disabled.")</p>
            </div>
        </div>
    </section>
@endsection
