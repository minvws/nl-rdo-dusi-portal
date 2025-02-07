@if($slot->isNotEmpty())
<section>
    <div>
        {{ $title }}

        <div>
            {{ $slot }}
        </div>
    </div>
</section>
@endif
