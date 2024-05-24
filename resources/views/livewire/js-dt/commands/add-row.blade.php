@php
    /** @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this */
    /** @var string $collectionName */
@endphp
<button
        class="btn btn-success"
        wire:click="$dispatchTo('{{ $this->relatedLivewireForm }}', 'open-form', {id: 0})"
><span class="bi bi-plus-square"></span></button>
