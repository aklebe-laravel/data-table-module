@php
    /** @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this */
    /** @var string $collectionName */
@endphp
<div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
            data-bs-toggle="dropdown" aria-expanded="false">
        <span class="bi bi-gear"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        @if($this->editable && $this->relatedLivewireForm && $this->canAddRow)
            <li>
                <button class="dropdown-item"
                        wire:click="$dispatchTo('{{ $this->relatedLivewireForm }}', 'open-form', {id: 0})">
                    {{ __('New Entry') }}
                </button>
            </li>
        @endif
        <li>
            <button class="dropdown-item"
                    wire:click="$dispatchTo('{{ $this->getName() }}', 'reset-filters')">
                {{ __('Reset all Filters') }}
            </button>
        </li>
    </ul>
</div>