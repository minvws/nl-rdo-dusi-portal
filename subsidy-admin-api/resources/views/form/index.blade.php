@extends('layouts.guest')

@section('content')
    <h1>Formulieren</h1>
    <form action="{{route("form.index")}}" method="get" class="line-form">
        <label for="subsidy_id">Search subsidy</label>
        <select id="subsidy_id" name="subsidy_id">
            @foreach($subsidies as $subsidy)
                <option value="{{$subsidy->id}}">{{$subsidy->title}}</option>
            @endforeach
        </select>
        <button type="submit">Search</button>
    </form>
    <br>
    <br>
    <div class="horizontal-scroll">
        <table>
            <thead>
            <tr>
                <th>Subsidy</th>
                <th>Version</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($forms as $form)
                <tr>
                    <td>{{$form->subsidy->title}}</td>
                    <td>{{$form->version}}</td>
                    <td>{{$form->status}}</td>
                    <td>
                        <a href="{{route("form.show", $form->id)}}">View</a>
                        <a href="{{route("form.edit", $form->id)}}">Edit</a>
                        <form action="{{route("form.destroy", $form->id)}}" method="post" class="line-form">
                            @csrf
                            @method("DELETE")
                            <div class="button-container">
                                <button type="submit">Delete</button>
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection


