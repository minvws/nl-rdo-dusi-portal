@extends('layouts.app')

@section('content')
    <h1>Show form</h1>
    <div class="horizontal-scroll">
        <table>
            <thead>
            <tr>
                <th>Subsidy</th>
                <th>Version</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$form->subsidy->title}}</td>
                    <td>{{$form->version}}</td>
                    <td>{{$form->status}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <br>
    <h2>Form Fields</h2>
    <div class="horizontal-scroll">
        <table>
            <thead>
            <tr>
                <th>Field</th>
                <th>Description</th>
                <th>Type</th>
                <th>Params</th>
                <th>is required</th>
                <th>Code</th>
                <th>Source</th>
            </tr>
            </thead>
            <tbody>
            @foreach($form->fields()->get() as $field)
                <tr>
                    <td>{{$field->title}}</td>
                    <td>{{$field->description}}</td>
                    <td>{{$field->type}}</td>
                    <td>
                        @php
                            var_dump(is_null($field->params) ? 'null' : $field->params);
                        @endphp
                    </td>
                    <td>{{$field->is_required}}</td>
                    <td>{{$field->code}}</td>
                    <td>{{$field->source}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
@endsection
