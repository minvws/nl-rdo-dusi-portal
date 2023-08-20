@extends('layouts.guest')

@section('content')
    <section class="layout-authentication">
        <div>
            <div class="warning">
                <span>@lang("Warning"):</span>
                <h1>@lang($title)</h1>
                <p>@lang("Something went wrong. Please contact our support desk at :email or :phone", [ 'email' => config('helpdesk.email'), 'phone' => config('helpdesk.phone') ])</p>

                <p class="de-emphazised">({{now()}})</p>
            </div>
        </div>
    </section>
@endsection
