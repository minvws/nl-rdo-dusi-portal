@if(config('app.env') === "production")
    <a href="/" class="ro-logo" aria-label="@lang('Logo Government of the Netherlands, go to the homepage')">
        <img src="{{ asset('images/logo/ro-logo.svg') }}" alt="">@lang('Dienst Uitvoering Subsidies aan Instellingen')
    </a>
@elseif(!Session::get('logo') )
    <a
        href="/?logo=1"
        class="ro-logo"
        aria-label="@lang('Logo Government of the Netherlands, go to the homepage')">
        <img src="{{ asset('images/logo/staging.gif') }}" alt=""/>@lang('Staging environment')
    </a>
@else
    <a
        href="/?logo=0"
        class="ro-logo"
        aria-label="@lang('Logo Government of the Netherlands, go to the homepage')">
        <img src="{{ asset('images/logo/ro-logo.svg') }}" alt=""/>@lang('Dienst Uitvoering Subsidies aan Instellingen')
    </a>
@endif
