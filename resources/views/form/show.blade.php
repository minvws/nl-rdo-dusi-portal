@extends('layouts.guest')

@section('content')
    <section>
        <div>
            <h1>Show form</h1>
            <h4>
                <a href='{{ route("form.edit", ["form" => $form]) }}'>Edit this form</a>
            </h4>
            <strong>or</strong>
            <div  class="delete">
                <form action="{{ route('form.destroy', ['form' => $form]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete this form</button>
                </form>
            </div>


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
                <tr>
                    <td>{{ $form->id }}</td>
                    <td>{{ \App\Models\Subsidy::find($form->subsidy_id)->title }}</td>
                    <td>{{ $form->version }}</td>
                    <td>{{ $form->status }}</td>
                </tr>
            </tbody>
        </table>
    </section>
@endsection
