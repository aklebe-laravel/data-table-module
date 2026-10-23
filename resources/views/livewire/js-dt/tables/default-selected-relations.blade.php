{{-- This table displays the selected rows only --}}
@php
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /** @var BaseDataTable $this */

    $collection = $this->getCollection($this::COLLECTION_NAME_SELECTED_ITEMS);
    $collectionName = $this::COLLECTION_NAME_SELECTED_ITEMS;
@endphp
<div>
    @include('data-table::livewire.js-dt.tables.default',[
        //'css_table' => 'table-selected-relations',
    ])
</div>