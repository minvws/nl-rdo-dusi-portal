@extends('layouts.guest')

@section('content')
    <section>
        <div>
            <h1>Index all forms</h1>
            <h2><a href="{{ route('form.create') }}">Create form</a></h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subsidy</th>
                    <th>Version</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($forms as $form)
                <tr>
                    <td><a href="{{ route('form.show', ['form' => $form->id]) }}">{{ $form->id }} </a></td>
                    <td>{{ \App\Models\Subsidy::find($form->subsidy_id)->title  }}</td>
                    <td>{{ $form->version }}</td>
                    <td>{{ $form->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
@endsection
