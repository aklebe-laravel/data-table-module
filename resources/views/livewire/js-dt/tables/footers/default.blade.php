@php
    /** @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this */
    /** @var string $collectionName */
@endphp
<div class="footer">
    <div class="grid grid-cols-12 gap-4 ">
        @include('data-table::livewire.js-dt.filters.pagination')
        @if($this->footerActions)
            <div class="col-span-12">
                @include($this->footerActions)
            </div>
        @endif
    </div>
</div>
