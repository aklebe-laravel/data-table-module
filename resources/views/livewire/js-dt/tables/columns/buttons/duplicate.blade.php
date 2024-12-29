@php
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /**
     * @var BaseDataTable $this
     * @var Model $item
     **/

    if (!$this->editable) {
        return;
    }
@endphp
<button
        class="btn btn-sm btn-outline-primary {{ data_get($this->mobileCssClasses, 'button', '') }}"
        wire:click="$dispatchTo('{{ $this->relatedLivewireForm }}', 'duplicate-and-open-form', {id: '{{ data_get($item, $this->columnNameId) }}' })"
        title="{{ __('Duplicate') }}"
><span class="bi bi-plus-square"></span></button>
