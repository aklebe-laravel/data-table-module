@php
    /** @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this */
    /** @var string $collectionName */
    $tagId = 'dt-header-setting-';
    $tagId.= app('system_base')->addUniqueCounter($tagId);
@endphp
<div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="{{ $tagId }}-actions"
            data-bs-toggle="dropdown" aria-expanded="false">
        <span class="bi bi-gear"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="{{ $tagId }}-actions">
        @if($this->editable && $this->relatedLivewireForm && $this->canAddRow)
            <li>
                <button class="dropdown-item button-new-entry"
                        wire:click="$dispatchTo('{{ $this->relatedLivewireForm }}', 'open-form', {id: 0})">
                    {{ __('New Entry') }}
                </button>
            </li>
        @endif
        <li>
            <button class="dropdown-item button-reset-all-filters"
                    wire:click="$dispatchTo('{{ $this->getName() }}', 'reset-filters')">
                {{ __('Reset all Filters') }}
            </button>
        </li>
    </ul>
</div>