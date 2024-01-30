@extends('layouts.app')

@section('page-title', __('Edit user').' '.$user->email)

@section('content')
{{--    <x-flash element="p" :only="['confirmation']"/>--}}

    <section class="form-view">
        <div>
            <h1>@lang("Edit user") {{ $user->email }}</h1>

            <div class="actions">
                <a class="button" href="{{ route('users.roles.index', $user->id) }}">@lang("Manage roles")</a>
            </div>

            @if ($errors->any())
                <div class="error">
                    <span>@lang("Error"):</span>
                    @lang("An error occurred while validating the entered data.")
                </div>
            @endif

            <x-auth.warning-user-inactive :user-active="$user->active"/>

            <dl>
                <div>
                    <dt>@lang("Name")</dt>
                    <dd>{{ $user->name }}</dd>
                </div>
                <div>
                    <dt>@lang("Email")</dt>
                    <dd>{{ $user->email }}</dd>
                </div>
                <div>
                    <dt>@lang("Created at")</dt>
                    <dd>{{ $user->created_at->format('d-m-Y H:i:s') }}</dd>
                </div>
            </dl>

            <form method=POST action="{{ route("users.update", $user->id)}}" class="horizontal-view">
                @csrf
                @method('PUT')
                <fieldset>
                    <legend>@lang("Data")</legend>
                    <div>
                        <label for="name">@lang("Name")</label>
                        <div>
                            <input id="name" name="name" placeholder="" type="text"
                                   value="{{ old('name', $user->name ?? '') }}" aria-describedby="name_error">
                            <x-input-error for="name" id="name_error"/>
                        </div>
                    </div>
                    <div>
                        <label for="email">@lang("Email")</label>
                        <div>
                            <input id="email" name="email" placeholder="" type="text"
                                   value="{{ old('email', $user->email ?? '') }}" aria-describedby="email_error">
                            <x-input-error for="email" id="email_error"/>
                        </div>
                    </div>
                    <div>
                        <label for="organisation_id">@lang("Organisation")</label>
                        <div>
                            <select id="organisation_id" name="organisation_id" aria-describedby="organisation_error">
                                <option value="">@lang("Select an organisation")</option>
                                @foreach ($organisations as $organisationId => $organisationName)
                                    <option value="{{ $organisationId }}" {{ old('organisation_id', $user->organisation_id) === $organisationId ? 'selected' : '' }}>{{ $organisationName }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="organisation_id" id="organisation_error" />
                        </div>
                    </div>
                </fieldset>

                <button type="submit">@lang("Update account data")</button>
            </form>

            <x-auth.form-user-active :user="$user" :update-active-route="route('users.update-active', $user->id)" />

            <x-auth.form-user-reset-credentials :reset-credentials-route="route('users.reset-credentials', $user->id)"/>
        </div>
    </section>
@endsection
