@php
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /** @var BaseDataTable $this */
    /** @var string $collectionName */
    $maxPage = $this->getPaginationMaxPage($collectionName);
    $currentPage = $this->getPaginationCurrentPage($collectionName);
    $rowsPerPage = $this->getPaginationRowsPerPage($collectionName);
    $pageLinkWire = '$dispatchTo("'.$this->getName().'","set-pagination-current-page", {"collectionName":"'.$collectionName.'", "index":"%d"})';
@endphp
@if ($maxPage >= 1)
    <livewire:system-base::pagination
            :maxPages="$maxPage"
            :currentPage="$currentPage"
            :itemsPerPage="$rowsPerPage"
            :pageLinkWire="$pageLinkWire"
            wire:key="{{ $this->getStableKey('pagination') }}"/>
@endif
