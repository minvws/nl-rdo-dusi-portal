<footer class="no-print">
    <div>
        <div class="two-thirds-one-third">
            <span class="slogan" lang="nl">@lang('De Rijksoverheid. Voor Nederland')</span>
            <nav aria-label="@lang('Footer Navigation')">
                <ul>
                    <li><a href="#">Something</a></li>
                </ul>
            </nav>
        </div>
        <div class="meta">
            <p>
                @lang('Version')
                <span id="application_version">{{ App\Http\Kernel::applicationVersion() }}</span>
            </p>
        </div>
    </div>
</footer>
