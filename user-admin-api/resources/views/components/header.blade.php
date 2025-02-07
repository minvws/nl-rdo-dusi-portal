<header class="no-print">
    <a href="#main-content" class="button skip-to-content">@lang('Skip to content')</a>

    <div class="page-meta no-print">
        @auth
            <div class="login-meta">
                <p>@lang("Logged in as"):
                    @if (\Laravel\Fortify\Features::hasSecurityFeatures())
                        <a href="{{route('profile.show')}}">{{ Auth::user()->name }}</a>
                    @else
                        {{ Auth::user()->name }}
                    @endif
                </p>
            </div>
        @endauth
    </div>

    <x-logo />

    {{ $slot }}
</header>
