@php
    /**
     * @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var Illuminate\Database\Eloquent\Model $item
     **/
    if (!$this->editable) {
        return;
    }
@endphp
<button
        class="btn btn-sm btn-outline-primary {{ data_get($this->mobileCssClasses, 'button', '') }}"
        wire:click="$dispatchTo('{{ $this->relatedLivewireForm }}', 'duplicate-and-open-form', {id: {{ data_get($item, 'id') }} })"
        title="{{ __('Duplicate') }}"
><span class="bi bi-plus-square"></span></button>
