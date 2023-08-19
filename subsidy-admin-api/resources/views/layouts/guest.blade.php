<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Lorem ipsum</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="img/favicon.ico" rel="shortcut icon">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>

<header>
    <a href="#main-content" class="button focus-only">Ga direct naar inhoud</a>
    <x-header />

    <nav aria-label="Hoofdnavigatie">
        <div>
            <ul>
                <li ><a href="/" aria-current="page">Hoofdpagina</a></li>
                <li><a href="/">Ipsum</a></li>
                <li><a href="/">Dolor</a></li>
            </ul>
        </div>
        <div>
        @if (Request::is('/'))
        <a href="{{ route('login') }}" class="button ro-icon ro-icon-user">Inloggen</a>
        @endif

    </div>
    </nav>
</header>

<main id="main-content" tabindex="-1">
    @yield('content')
</main>


<footer>
    <div>
        <span>De Rijksoverheid. Voor Nederland</span>
    </div>
</footer>
</html>
