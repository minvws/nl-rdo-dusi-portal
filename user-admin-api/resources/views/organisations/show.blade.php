@extends('layouts.app')

@section('page-title', __('Edit organisation').' '.$organisation->name)

@section('content')
    <x-flash element="p" :only="['confirmation']"/>

    <section class="form-view">
        <div>
            <h1>@lang("Edit organisation") {{ $organisation->email }}</h1>

            @if ($errors->any())
                <div class="error">
                    <span>@lang("Error"):</span>
                    @lang("An error occurred while validating the entered data.")
                </div>
            @endif

            <dl>
                <div>
                    <dt>@lang("Name")</dt>
                    <dd>{{ $organisation->name }}</dd>
                </div>
                <div>
                    <dt>@lang("Created at")</dt>
                    <dd>{{ $organisation->created_at->format('Y-d-m H:i:s') }}</dd>
                </div>
            </dl>

            <form method=POST action="{{ route("organisations.update", $organisation->id)}}" class="horizontal-view">
                @csrf
                @method('PUT')
                <fieldset>
                    <legend>@lang("Data")</legend>
                    <div>
                        <label for="name">@lang("Name")</label>
                        <div>
                            <input id="name" name="name" placeholder="" type="text"
                                   value="{{ $organisation->name ?? '' }}" aria-describedby="name_error">
                            <x-input-error for="name" id="name_error"/>
                        </div>
                    </div>
                </fieldset>
                <button type="submit">@lang("Update organisation data")</button>
            </form>
        </div>
    </section>
@endsection
