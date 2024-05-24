@php
    /** @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this */
    /** @var string $collectionName */
@endphp
<div class="footer">
    <div class="row">
        @include('data-table::livewire.js-dt.filters.pagination')
        @if($this->footerActions)
            <div class="col-12">
                @include($this->footerActions)
            </div>
        @endif
    </div>
</div>
