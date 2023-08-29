@props(['element' => 'div', 'only' => null])

@if ($flash = Session::get('flash'))
    <{{ $element }}
        class="{{ $flash->getType() }}"
        data-message-type="{{ $flash->getType() }}"
        role="status"
    >

    <span>{{ $getNotificationTypeSpan($flash->getType()->value) }}</span>
        @if (is_scalar($flash->getMessage()))
            @lang($flash->getMessage())
        @elseif(is_array($flash->getMessage()))
            <ul>
                @foreach ($flash->getMessage() as $message)
                    <li>@lang($message)</li>
                @endforeach
            </ul>
        @endif
    </{{ $element }}>
@endif
