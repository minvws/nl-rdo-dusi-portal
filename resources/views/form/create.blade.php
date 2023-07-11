@extends('layouts.guest')
@section('content')
    <section>
        <div><h1>Create form</h1></div>
        <form action="{{ route('form.store') }}" method="POST">
            @csrf
            <label for="subsidy_id">Subsidy:</label>
            <select name="subsidy_id" id="subsidy_id" required>
                @foreach ($subsidies as $subsidy)
                    <option value="{{ $subsidy->id }}" >
                        {{ $subsidy->title }}
                    </option>
                @endforeach
            </select>

            <input type="submit" value="Create Form">
        </form>
    </section>
@endsection
