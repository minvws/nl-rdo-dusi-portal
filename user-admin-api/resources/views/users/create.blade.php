@extends('layouts.app')

@section('page-title', __('Create new user'))

@section('content')
<section class="form-view">
    <div>
        <h1>@lang('Create new user')</h1>

        <form method=POST action="{{ route("users.store")}}" class="horizontal-view">
            @csrf
            <fieldset>
                <legend>@lang("Data")</legend>
                <div>
                    <label for="name">@lang("Name")</label>
                    <div>
                        <input id="name" name="name" placeholder="Jan de Vries" type="text" value="{{ old('name') }}" aria-describedby="name_error">
                        <x-input-error for="name" id="name_error" />
                    </div>
                </div>
                <div>
                    <label for="email">@lang("Email")</label>
                    <div>
                        <input id="email" name="email" placeholder="jandevries@email.nl" type="email" value="{{ old('email') }}" aria-describedby="email_error">
                        <x-input-error for="email" id="email_error" />
                    </div>
                </div>
            </fieldset>

            <button type="submit">@lang("Submit")</button>
        </form>
    </div>
</section>
@endsection
