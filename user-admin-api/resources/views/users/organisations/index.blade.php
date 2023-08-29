@extends('layouts.app')

@section('page-title', __('User management'))

@section('content')
{{--    <x-flash element="p" />--}}

    <section>
        <div>
            <h1>@lang("User organisations management")</h1>

            <x-filter-form-section/>

            @if ($organisations->isEmpty())
                <div class="system-notification" role="group" aria-label="@lang('system notification')">
                    <p>@lang("Currently not connected to an organisation.")</p>
                    <p>@lang("Connect the user to an organisation using the form below.")</p>
                </div>
            @else
                <p class="hidden" id="name_status_text"></p>
                <div class="horizontal-scroll">
                    <table id="user-overview-table">
                        <caption>@lang("Account overview:")</caption>
                        <thead>
                        <tr>
                            <th scope="col"> @sortablelink('name', __('Organisation')) </th>
                            <th scope="col">@lang('Roles')</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($organisations as $organisation)
                            <tr>
                                <td>
                                    {{ $organisation->name }}
                                </td>
                                <td>
                                    {{ $organisation->pivot->role_name }}
                                </td>
                                <td class="nowrap">
                                    <form action="{{ route('users.organisations.destroy', ['user' => $user->id, 'organisation' => $organisation->id]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">@lang('Delete')</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $organisations->appends(\Request::except('page', '_token'))->render() !!}
                </div>
            @endif

            <form method=POST action="{{ route("users.organisations.store", ['user' => $user->id])}}" class="horizontal-view">
                @csrf
                <fieldset>
                    <legend>@lang("Add organisation to user")</legend>
                    <div>
                        <label for="organisation_id">@lang("Organisation")</label>
                        <div>
                            <select id="organisation_id" name="organisation_id" aria-describedby="organisation_error">
                                <option value="">@lang("Select an organisation")</option>
                                @foreach ($allOrganisations as $organisationId => $organisationName)
                                    <option value="{{ $organisationId }}" {{ old('organisation') === $organisationId ? 'selected' : '' }}>{{ $organisationName }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="organisation_id" id="organisation_error" />
                        </div>
                    </div>
                    <div>
                        <label for="role">@lang("Role")</label>
                        <div>
                            <select id="role" name="role" aria-describedby="role_error">
                                <option value="">@lang("Select a role")</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ $role }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="role" id="role_error" />
                        </div>
                    </div>
                </fieldset>

                <button type="submit">@lang("Submit")</button>
            </form>
        </div>
    </section>
@endsection
