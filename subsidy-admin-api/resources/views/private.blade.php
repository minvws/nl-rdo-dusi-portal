@extends('layouts.guest')

@section('content')
    <section>
        <div><h1>Logged in</h1></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </section>
@endsection
