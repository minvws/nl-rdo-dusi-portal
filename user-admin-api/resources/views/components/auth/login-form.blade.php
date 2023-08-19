<form
    class="horizontal-view"
    method="POST"
    action="{{ route('login') }}"
    autocomplete="off"
>
    @csrf

    <div>
        <label for="email">@lang('Email')</label>
        <input id="email" type="email" name="email" :value="old('email')" required autocomplete="email" autofocus>
    </div>

    <div>
        <label for="password">@lang('Password')</label>
        <input id="password" type="password" name="password" required autocomplete="current-password">
    </div>

    @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}">@lang('Forgot your password?')</a>
    @endif

    <x-button>@lang('Login')</x-button>
</form>
