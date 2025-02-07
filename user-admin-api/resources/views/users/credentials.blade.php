@extends('layouts.app')

@section('page-title', __('New user'))

@section('content')
{{--    @if($message)--}}
{{--        <p class="confirmation" role="status">{{ $message }}</p>--}}
{{--    @endif--}}

    <section>
        <div id="created-user-form">

            <dl>
                <div>
                    <dt>@lang('Name')</dt>
                    <dd>{{ $user->name }}</dd>
                </div>
                <div>
                    <dt>@lang('Email')</dt>
                    <dd>{{ $user->email }}</dd>
                </div>
                @if(!empty($password))
                <div>
                    <dt>@lang('Initial password')</dt>
                    <dd id="initialPassword">{{ $password }}</dd>
                </div>
                @endif
                @if($twoFactorAuthenticationReset)
                <div>
                    <dt>@lang('QR-code') 2FA</dt>
                    <dd id="userQr">
                        {!! $user->twoFactorQrCodeSvgWithAria() !!}
                    </dd>
                </div>
                @endif
            </dl>

            <button id="executePrint" class="no-print">@lang('Print document')</button>
        </div>
    </section>
@endsection
