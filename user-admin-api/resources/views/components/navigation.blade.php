<nav
    data-open-label="Menu"
    data-close-label="Sluit menu"
    data-media="(min-width: 50rem)"
    class="collapsible"
    aria-label="@lang('Main Navigation')"
    id="main-nav">
    <div class="collapsing-element">
        <ul>
            <x-nav-item :route="'home'"><span class="icon icon-home"></span>@lang('Homepage') </x-nav-item>
        </ul>

        @auth
            <ul>
                <li>
                    <x-auth.navigation-logout-button/>
                </li>
            </ul>
        @endauth
    </div>
</nav>

