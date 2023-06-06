@extends('layouts.app')

@section('content')
    <article>
        <div class="filter">
            <form action="" method="get">
                <label for="voorbeeld-text-input-1">Status van de aanvraag</label>
                <select id="judgement" name="judgement">
                    @foreach ($judgements as $judgement)
                        <option @if($judgement->value === old('judgement', 'pending')) selected @endif value="{{$judgement}}">{{$judgement}}</option>
                    @endforeach
                </select>
                <button type="submit">Filter</button>
            </form>
        </div>
        <div class="horizontal-scroll">
            <table>
                <caption>Applications</caption>
                <thead>
                <tr>
                    <th scope="col">id</th>
                    <th scope="col">created_at</th>
                    <th scope="col">judgement</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($applications as $application)
                    <tr>
                        <td>{{$application->id}}</td>
                        <td>{{$application->created_at}}</td>
                        <td>{{$application->judgement}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </article>
@endsection
