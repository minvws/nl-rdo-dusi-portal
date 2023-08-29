<form method="POST" action="{{ $updateActiveRoute }}" class="horizontal-view">
    @csrf
    @method('PUT')
    <fieldset>
        <legend>{{__('Active')}}</legend>

        <div>
            <label for="active_until">@lang('Active until')</label>
            <div>
                <input id="active_until" name="active_until" placeholder="" type="datetime-local" value="{{ $user->active_until }}" aria-describedby="active_until_error">
                <x-input-error for="active_until" id="active_until_error" :errors="$errors->user_update_active" />
            </div>
        </div>
    </fieldset>
    <button type="submit">@lang("Update active until")</button>
</form>
