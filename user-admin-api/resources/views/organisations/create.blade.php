@extends('layouts.app')

@section('page-title', __('Create new organisation'))

@section('content')
<section class="form-view">
    <div>
        <h1>@lang('Create new organisation')</h1>

        <form method=POST action="{{ route("organisations.store") }}" class="horizontal-view">
            @csrf
            <fieldset>
                <legend>@lang("Data")</legend>
                <div>
                    <label for="name">@lang("Name")</label>
                    <div>
                        <input id="name" name="name" placeholder="Organisatie A" type="text" value="{{ old('name') }}" aria-describedby="name_error">
                        <x-input-error for="name" id="name_error" />
                    </div>
                </div>
            </fieldset>

            <button type="submit">@lang("Submit")</button>
        </form>
    </div>
</section>
@endsection
