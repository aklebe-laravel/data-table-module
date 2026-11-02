{{-- This table displays the unselected rows only --}}
@php
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /** @var BaseDataTable $this */

    $collection = $this->getCollection($this::COLLECTION_NAME_UNSELECTED_ITEMS);
    $collectionName = $this::COLLECTION_NAME_UNSELECTED_ITEMS;
@endphp
<div>
    @include($this->tableView, [
        //'css_table' => 'table-unselected-relations',
    ])
</div>
