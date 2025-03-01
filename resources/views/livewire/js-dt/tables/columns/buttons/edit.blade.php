@php
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /**
     * @var BaseDataTable $this
     * @var Model $item
     **/

    // relatedLivewireForm is needed for edit
    if (!$this->editable || !$this->relatedLivewireForm) {
        return;
    }
@endphp
<button
        class="btn btn-sm btn-outline-primary btn-edit {{ data_get($this->mobileCssClasses, 'button', '') }}"
        wire:click="$dispatchTo('{{ $this->relatedLivewireForm }}', 'open-form', {id: '{{ data_get($item, $this->columnNameId) }}' })"
        title="{{ __('Edit') }}"
>
    <span class="bi bi-gear"></span>
</button>
