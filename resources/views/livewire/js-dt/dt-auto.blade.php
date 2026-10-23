@php
    /** @var BaseDataTable $this */

    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    $dtAutoKey = 'dt-auto-'.$this->getEloquentModelName();
    $dtAutoCounter = app('system_base')->addUniqueCounter($dtAutoKey);
    $hasSelectedCollection = isset($this->enabledCollectionNames[$this::COLLECTION_NAME_SELECTED_ITEMS]) && ($this->selectedItems);
    $hasUnselectedCollection = isset($this->enabledCollectionNames[$this::COLLECTION_NAME_UNSELECTED_ITEMS]);
    $hasTwoTables = $hasSelectedCollection && $hasUnselectedCollection;
@endphp
<div class="data-table-2t-default {{ $dtAutoKey.'-'.$dtAutoCounter }}">

    @include('data-table::inc.messages')

    <div>
        <h1>{{ data_get($this, 'title', 'Datatable ' . $this->getEloquentModelName()) }}</h1>

        {{-- This type of data-table should be enabled and selected items should be exist --}}
        @if ($hasSelectedCollection)
            @include('data-table::livewire.js-dt.tables.default-selected-relations', $hasTwoTables ? ['css_table' => 'table-selected-relations'] : [])
        @endif

        {{-- This type of data-table should be enabled --}}
        @if ($hasUnselectedCollection)
            {{-- This is the standard table for non selected items --}}
            @include('data-table::livewire.js-dt.tables.default-unselected-relations', $hasTwoTables ? ['css_table' => 'table-unselected-relations'] : [])
        @endif

        {{-- At least, in case of no selecteables tables needed --}}
        @if (isset($this->enabledCollectionNames[$this::COLLECTION_NAME_DEFAULT]) && (!$hasUnselectedCollection))
            {{-- This is the standard table for non selected items --}}
            @include('data-table::livewire.js-dt.tables.default')
        @endif

    </div>

</div>
