<form method="POST" action="{{ $resetCredentialsRoute }}" class="horizontal-view">
    @csrf

    <fieldset>
        <legend>{{__('Reset login credentials')}}</legend>
        <div class="checkbox">
            <input type="checkbox" id="reset_password" name="reset_password">
            <label for="reset_password">{{__('I want to reset the password')}}</label>
        </div>
        <x-input-error for="reset_password" id="reset_password_error" :errors="$errors->user_reset_credentials"/>

        <div class="checkbox">
            <input type="checkbox" id="reset_2fa" name="reset_2fa">
            <label for="reset_2fa">{{__('I want to reset 2FA')}}</label>
        </div>
        <x-input-error for="reset_2fa" id="reset_2fa_error" :errors="$errors->user_reset_credentials" />
    </fieldset>
    <button type="submit">@lang("Reset credentials")</button>
</form>
