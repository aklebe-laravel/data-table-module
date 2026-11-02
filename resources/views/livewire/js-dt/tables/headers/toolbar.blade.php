{{--
    Header in one row: all header elements (filters and addHeaderElement()) ordered by 'position'.
    Wraps on small screens. 'css_group' of an element is the css of its wrapper ('ml-auto', 'flex-1 max-w-md', 'hidden').
    Use: $headerView = BaseDataTable::HEADER_VIEW_TOOLBAR
--}}
@php
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /** @var BaseDataTable $this */
    /** @var string $collectionName */
@endphp
<div class="header toolbar px-2 py-2">
    <div class="w-full flex flex-wrap items-center gap-2">
        @foreach($this->getOrderedFilterElementConfig() as $_config)
            <div class="{{ $_config['css_group'] ?? '' }}">
                @include($_config['view'])
            </div>
        @endforeach
    </div>
    @if ($this->headerPagination)
        <div class="w-full mt-2 grid grid-cols-2 gap-4 text-sm">
            <div class="text-secondary-500 text-start">
                @include('data-table::livewire.js-dt.filters.pagination', ['paginationPosition' => 'header'])
            </div>
            <div class="text-secondary-500 text-end">
                {{ sprintf(__("Entries found: %s"), $this->getPaginationMaxRows($collectionName)) }}
            </div>
        </div>
    @endif
</div>
