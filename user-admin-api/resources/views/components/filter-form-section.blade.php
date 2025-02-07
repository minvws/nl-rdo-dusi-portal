@props([
    'filterPlaceholder' => 'E.g search by username',
])

<section class="filter">
    <div>
        <button aria-expanded="false" data-show-filters-label="Toon filters">Toon filters</button>
    </div>

    <form action="" method="GET" hidden>
        <label for="filter">@lang('Keyword')</label>
        <input id="filter" name="filter" placeholder="{{ $filterPlaceholder }}" type="text"
               value="{{ Request::get('filter') }}">
        <button type="submit">@lang("Filter")</button>
    </form>
</section>
