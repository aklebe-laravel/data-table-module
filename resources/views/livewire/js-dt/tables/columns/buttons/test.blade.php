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
        class="btn btn-sm btn-outline-dark filtered dt-interaction {{ data_get($this->mobileCssClasses, 'button', '') }}"
        wire:click="$dispatchSelf('module-action', {'action':'test-my-event','itemId':'{{ data_get($item, $this->columnNameId) }}'})"
        title="{{ __('Test') }}"
>
    <x-lucide-circle-question-mark class="h-5 w-5"/>
</button>
