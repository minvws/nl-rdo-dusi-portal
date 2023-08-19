@if(config('app.env') === "production")
    <a href="/" class="ro-logo" aria-label="{{__('Rijksoverheid logo, go to the VIEP homepage')}}">
        <img src="/huisstijl/img/ro-logo.svg" alt="Logo Rijksoverheid">
        VIEP
    </a>
@elseif(!Session::get('logo') )
    <a
        href="/?logo=1"
        class="ro-logo"
        aria-label="{{__('Rijksoverheid logo, go to the VIEP homepage') }}">
        <img src="/img/staging.gif" alt="" />
        Assessment Web {{config('app.env')}}
    </a>
@else
    <a
        href="/?logo=0"
        class="ro-logo"
        aria-label="{{__('Rijksoverheid logo, go to the VIEP homepage') }}">
        <img src="/img/ro-logo.svg" alt="Logo Rijksoverheid" />
        Assessment Web
    </a>
@endif
