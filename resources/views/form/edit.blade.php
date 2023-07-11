@extends('layouts.guest')
@section('content')
    <section>
        <div><h1>Edit form</h1></div>
        <form action="{{ route('form.update', ['form' => $form]) }}" method="POST">
            @csrf
            @method('PUT')
            <label for="subsidy_id">Subsidy:</label>
            <select name="subsidy_id" id="subsidy_id" required>
                @foreach ($subsidies as $subsidy)
                    <option value="{{ $subsidy->id }}" {{ $subsidy->id == $form->subsidy_id ? 'selected' : '' }}>
                        {{ $subsidy->title }}
                    </option>
                @endforeach
            </select>

            <label for="version">Version:</label>
            <input type="text" name="version" id="version" value="{{ $form->version }}" required>

            <label for="status">Status:</label>
            <select name="status" id="status" required>
                @foreach ($statusOptions as $optionValue)
                    <option value="{{ $optionValue }}" {{ $optionValue == $form->status ? 'selected' : '' }}>
                        {{ $optionValue }}
                    </option>
                @endforeach
            </select>
            <input type="submit" value="Update Form">
        </form>
    </section>
@endsection
