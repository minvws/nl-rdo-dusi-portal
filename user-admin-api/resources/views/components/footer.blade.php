<footer class="no-print">
    <div>
        <div class="two-thirds-one-third">
            <span class="slogan" lang="nl">@lang('De Rijksoverheid. Voor Nederland')</span>
            <nav aria-label="@lang('Footer Navigation')">
                <ul>
                </ul>
            </nav>
        </div>
        <div class="meta">
            <p>
                @lang('Version')
                <span id="application_version">{{ MinVWS\DUSi\User\Admin\API\Http\Kernel::applicationVersion() }}</span>
            </p>
        </div>
    </div>
</footer>
