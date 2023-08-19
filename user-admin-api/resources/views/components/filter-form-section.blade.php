<section class="filter">
    <div>
        <button aria-expanded="false" data-show-filters-label="Toon filters">Toon filters</button>
    </div>

    <form action="" method="POST">
        @csrf
        <label for="filter">@lang('Keyword')</label>
        <input id="filter" name="filter" placeholder="@lang('E.g search by username')" type="text"
               value="{{Request::get('filter')}}">
        <button type="submit">@lang("Filter")</button>
    </form>
</section>
