@extends('layouts.app')

@section('page-title', __('Organisation management'))

@section('content')
    <x-flash element="p" />

    <section>
        <div>
            <h1>@lang("Organisation management")</h1>

            <div class="actions">
                <a class="button" href="{{route('organisations.create')}}">@lang("Create new organisation")</a>
            </div>

            <x-filter-form-section/>

            @if ($organisations->isEmpty())
                <div class="system-notification" role="group" aria-label="@lang('system notification')">
                    <p>@lang("Currently no data available.")</p>
                    <p>@lang("Add a new organisation by using the 'Create new organisation' button.")</p>
                </div>
            @else
                <p class="hidden" id="name_status_text"></p>
                <div class="horizontal-scroll">
                    <table id="user-overview-table">
                        <caption>@lang("Organisations overview:")</caption>
                        <thead>
                        <tr>
                            <th scope="col"> @sortablelink('name', __('Name')) </th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($organisations as $organisation)
                            <tr>
                                <td>
                                    {{ $organisation->name }}
                                </td>
                                <td class="nowrap">
                                    <a href="{{route('organisations.show', $organisation->id)}}" class="button">@lang('Edit')</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $organisations->appends(\Request::except('page', '_token'))->render() !!}
                </div>
            @endif
        </div>
    </section>
@endsection
