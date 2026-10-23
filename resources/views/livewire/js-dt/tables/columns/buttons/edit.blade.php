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
        class="btn btn-sm btn-outline-primary text-primary-500 btn-edit dt-interaction {{ data_get($this->mobileCssClasses, 'button', '') }}"
        wire:click="$dispatchTo('{{ $this->relatedLivewireForm }}', 'open-form', {id: '{{ data_get($item, $this->columnNameId) }}' })"
        title="{{ __('Edit') }}"
>
    <x-lucide-notebook-pen class="h-5 w-5"/>
</button>
