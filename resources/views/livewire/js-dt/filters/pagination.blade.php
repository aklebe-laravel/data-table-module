@php
    /** @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this */
    /** @var string $collectionName */
    $maxPage = $this->getPaginationMaxPage($collectionName);
    $livewireKey = \Modules\SystemBase\app\Services\LivewireService::getKey($this->getName().'-pagination');
    $paginationData = [
        'maxPages'    => $maxPage,
        'currentPage' => $this->getPaginationCurrentPage($collectionName),
        'itemsPerPage' => $this->getPaginationRowsPerPage($collectionName),
        'pageLinkWire' => '$dispatchTo("'.$this->getName().'","set-pagination-current-page", {"collectionName":"'.$collectionName.'", "index":"%d"})',
    ];

@endphp
@if ($maxPage > 1)
    <div class="col-12 col-lg-6 text-center text-lg-start">
        {{--Pagination--}}
        @livewire('system-base::pagination', $paginationData, key($livewireKey))
    </div>
@endif
