@php
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /** @var BaseDataTable $this */
    /** @var string $collectionName */
@endphp
<div class="header p-4 bg-surface-200/40">
    <div class="w-full mb-1 grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($this->getOrderedFilterElementConfig() as $_config)
            <div class="{{ $_config['css_group'] ?? '' }}">
                @include($_config['view'])
            </div>
        @endforeach
    </div>
    <div class="w-full grid grid-cols-2 gap-4 text-sm">
        <div class="text-secondary-500 text-start">
            @include('data-table::livewire.js-dt.filters.pagination')
        </div>
        <div class="text-secondary-500 text-end">
            {{ sprintf(__("Entries found: %s"), $this->getPaginationMaxRows($collectionName)) }}
        </div>
    </div>
</div>