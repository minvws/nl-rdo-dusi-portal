@extends('layouts.app')

@section('content')
    <article>
        <div>
            <h1>@lang('DUS-I User Admin Portal')</h1>
            <p>@lang('Portal to manage users.')</p>
        </div>
    </article>

    <section class="background-color-offset">
        <div>
            <h1>@lang('Related websites')</h1>
            <div>
                <nav aria-label="@lang('Related websites')">
                    <ul>
                        <li><a href="https://www.dus-i.nl/" lang="nl" rel="external">Dienst Uitvoering Subsidies aan Instellingen</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </section>
@endsection
