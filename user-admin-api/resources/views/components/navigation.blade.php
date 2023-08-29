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

            @auth
                @can('viewAny', \MinVWS\DUSi\User\Admin\API\Models\Organisation::class)
                    <x-nav-item :route="'organisations.index'">@lang('Organisations') </x-nav-item>
                @endcan
                @can('viewAny', \MinVWS\DUSi\User\Admin\API\Models\User::class)
                    <x-nav-item :route="'users.index'">@lang('Users') </x-nav-item>
                @endcan
            @endauth
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

