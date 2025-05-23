@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'error']) }}>
        <p><span>@lang('Error:')</span> @lang('Whoops! Something went wrong.')</p>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
