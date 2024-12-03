@php
    /** @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this */
    /** @var string $collectionName */
@endphp
<div class="container-fluid text-center header">
    <div class="row">
        @foreach($this->getOrderedFilterElementConfig() as $_config)
            <div class="{{ $_config['css_group'] }}">
                @include($_config['view'])
            </div>
        @endforeach
    </div>
</div>
<div class="container-fluid">
    <div class="row mt-2">
        @include('data-table::livewire.js-dt.filters.pagination')
        <div class="col-12 col-lg-6 text-secondary text-center text-lg-end">
            {{ sprintf(__("Entries found: %s"), $this->getPaginationMaxRows($collectionName)) }}
        </div>
    </div>
</div>
