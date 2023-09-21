@extends('layouts.app')

@section('page-title', __('Edit roles for user'))

@section('content')
    <section>
        <div>
            <h1>@lang("Edit roles for user") {{ $user->email }}</h1>

{{--            <x-filter-form-section/>--}}

            @if ($userRoles->isEmpty())
                <div class="system-notification" role="group" aria-label="@lang('system notification')">
                    <p>@lang("Currently not connected to any roles.")</p>
                    <p>@lang("Connect the user to a role using the form below.")</p>
                </div>
            @else
                <p class="hidden" id="name_status_text"></p>
                <div class="horizontal-scroll">
                    <table id="user-overview-table">
                        <caption>@lang("User roles overview:")</caption>
                        <thead>
                        <tr>
                            <th scope="col">@lang('Roles')</th>
                            <th scope="col"> @lang('Subsidy') </th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($userRoles as $role)
                            <tr>
                                <td>
                                    {{ $role->name }}
                                </td>
                                <td>
                                    {{ $subsidies[$role->pivot->subsidy_id] ?? 'Every subsidy' }}
                                </td>
                                <td class="nowrap">
                                    <form action="{{ route('users.roles.destroy', ['user' => $user->id, 'role' => $role->name ]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="role" value="{{ $role->name }}">
                                        <input type="hidden" name="subsidy_id" value="{{ $role->pivot->subsidy_id }}">
                                        <button type="submit">@lang('Delete')</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $userRoles->appends(\Request::except('page', '_token'))->render() !!}
                </div>
            @endif

            <form method=POST action="{{ route("users.roles.store", ['user' => $user->id])}}" class="horizontal-view">
                @csrf
                <fieldset>
                    <legend>@lang("Add role to user")</legend>
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
                    <div>
                        <label for="subsidy_id">@lang("Subsidy")</label>
                        <div>
                            <select id="subsidy_id" name="subsidy_id" aria-describedby="subsidy_error">
                                <option value="">@lang("Select an subsidy")</option>
                                @foreach ($subsidies as $subsidyId => $subsidyTitle)
                                    <option value="{{ $subsidyId }}" {{ old('subsidy_id') === $subsidyId ? 'selected' : '' }}>{{ $subsidyTitle }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="subsidy_id" id="subsidy_error" />
                        </div>
                    </div>
                </fieldset>

                <button type="submit">@lang("Submit")</button>
            </form>
        </div>
    </section>
@endsection
