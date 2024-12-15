@php
    /**
     * @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var Illuminate\Database\Eloquent\Model $item
     **/

    // path for messageBox.config
    $jsMessageBoxDeleteItemPath = \Illuminate\Support\Str::snake($this->getEloquentModelName(), '-') . '.data-table.delete';
    if (!$this->editable || !$this->removable || !$this->canItemRemoved($item)) {
        return;
    }
@endphp
{{--If confirmation exists, use the javascript confirmation part ...--}}
@if(config('message-boxes.' . $jsMessageBoxDeleteItemPath))
    <button
            x-on:click="messageBox.show('{{ $jsMessageBoxDeleteItemPath }}', {'deleteItem': {livewire_id: '{{ $this->getId() }}', name: '{{ $this->getName() }}', item_id: {{ data_get($item, 'id') }}}})"
            class="btn btn-sm btn-outline-danger {{ data_get($this->mobileCssClasses, 'button', '') }}"
            title="{{ __('Delete') }}"
    >
        <span class="bi bi-trash"></span>
    </button>
    {{--otherwise instant livewire...--}}
@else

    @php
        // @todo: add to config/parameter!
        // true: always show confirmation
        // false: if there is no message box defined for this model, delete instant without confirmation
        $_forceDeleteConfirmation = true;
    @endphp

    @if($_forceDeleteConfirmation)
        <button
                x-on:click="messageBox.show('__default__.data-table.delete', {'delete-item': {livewire_id: '{{ $this->getId() }}', name: '{{ $this->getName() }}', item_id: {{ data_get($item, 'id') }}}})"
                class="btn btn-sm btn-outline-danger {{ data_get($this->mobileCssClasses, 'button', '') }}"
                title="{{ __('Delete') }}"
        >
            <span class="bi bi-trash"></span>
        </button>
    @else
        <button
                wire:click="deleteItem('{{ $this->id }}', '{{ data_get($item, 'id') }}')"
                class="btn btn-sm btn-danger {{ data_get($this->mobileCssClasses, 'button', '') }}"
                title="{{ __('Delete') }}"
        >
            <span class="bi bi-trash"></span>
        </button>
    @endif

@endif
