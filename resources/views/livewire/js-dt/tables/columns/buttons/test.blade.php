@php
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /**
     * @var BaseDataTable $this
     * @var Model $item
     **/

    if (!$this->editable || !$this->relatedLivewireForm) {
        return;
    }
@endphp
<button
        class="btn btn-sm btn-outline-dark bg-warning-subtle {{ data_get($this->mobileCssClasses, 'button', '') }}"
        wire:click="$dispatchSelf('module-action', {'action':'test-my-event','itemId':'{{ data_get($item, $this->columnNameId) }}'})"
        title="{{ __('Test') }}"
>
    <span class="bi bi-question-circle"></span>
</button>
