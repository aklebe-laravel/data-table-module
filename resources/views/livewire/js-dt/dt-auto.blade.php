@php
    /** @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this */
    $dtAutoKey = 'dt-auto-'.$this->getModelName();
    $dtAutoCounter = app('system_base')->addUniqueCounter($dtAutoKey);
@endphp
<div class="data-table-2t-default {{ $dtAutoKey.'-'.$dtAutoCounter }}">

    @include('data-table::inc.messages')

    <div>
        <h1>{{ data_get($this, 'title', 'Datatable ' . $this->getModelName()) }}</h1>

        {{-- This type of data-table should be enabled --}}
        @if (isset($this->enabledCollectionNames[$this::COLLECTION_NAME_SELECTED_ITEMS]))
            {{-- Show this one if selections exists --}}
            @if ($this->selectedItems)
                @include('data-table::livewire.js-dt.tables.default-selected-relations')
            @endif
        @endif

        {{-- This type of data-table should be enabled --}}
        @if (isset($this->enabledCollectionNames[$this::COLLECTION_NAME_UNSELECTED_ITEMS]))
            {{-- This is the standard table for non selected items --}}
            @include('data-table::livewire.js-dt.tables.default-unselected-relations')
        @endif

        {{-- At least, in case of no selecteables tables needed --}}
        @if ((isset($this->enabledCollectionNames[$this::COLLECTION_NAME_DEFAULT]))
            && (!isset($this->enabledCollectionNames[$this::COLLECTION_NAME_UNSELECTED_ITEMS])))
            {{-- This is the standard table for non selected items --}}
            @include('data-table::livewire.js-dt.tables.default')
        @endif

    </div>

</div>
