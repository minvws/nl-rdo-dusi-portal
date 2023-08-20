<form action="{{ route('logout') }}" method="POST" class="inline">
    @csrf
    <button type="submit">@lang('Logout')</button>
</form>
