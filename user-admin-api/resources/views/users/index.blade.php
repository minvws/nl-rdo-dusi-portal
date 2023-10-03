@extends('layouts.app')

@section('page-title', __('User management'))

@section('content')
    <section>
        <div>
            <h1>@lang("User management")</h1>

            <div class="actions">
                <a class="button" href="{{ route('users.create') }}">@lang("Create new user")</a>
            </div>

            <x-filter-form-section :filter-placeholder="__('E.g search by email or name')"/>

            @if ($users->isEmpty())
                <div class="system-notification" role="group" aria-label="@lang('system notification')">
                    <p>@lang("Currently no data available.")</p>
                    <p>@lang("Add a new user by using the 'Create new user' button.")</p>
                </div>
            @else
                <p class="hidden" id="name_status_text"></p>
                <div class="horizontal-scroll">
                    <table id="user-overview-table">
                        <caption>@lang("User accounts overview:")</caption>
                        <thead>
                        <tr>
                            <th scope="col"> @sortablelink('name', __('Name')) </th>
                            <th scope="col"> @sortablelink('email', __('Email')) </th>
                            <th scope="col">@lang('Organisation')</th>
                            <th scope="col">@lang('Active until')</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    {{ $user->name }}
                                    @if (!$user->active)
                                        <small>(@lang('deactivated'))</small>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->organisation)
                                        <a href="{{ route('organisations.show', $user->organisation->id) }}">{{ $user->organisation->name }}</a>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->active_until)
                                        <span id="authorized_until_hours-{{$user->id}}">{{ $user->active_until->format("d-m-Y H:i:s") }}</span>
                                    @endif
                                </td>
                                <td class="nowrap">
                                    <a href="{{route('users.show', $user->id)}}" class="button"
                                       id="editUser">@lang('Edit')</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $users->appends(\Request::except('page', '_token'))->render() !!}
                </div>
            @endif
        </div>
    </section>
@endsection
