@php
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /**
     * @var BaseDataTable $this
     * @var Model $item
     **/

    // path for messageBox.config
    $jsMessageBoxDeleteItemPath = \Illuminate\Support\Str::snake($this->getEloquentModelName(), '-') . '.data-table.delete';
    if (!$this->editable || !$this->removable || !$this->canItemRemoved($item)) {
        return;
    }

    $messageBoxParamsDelete = [
        'delete-item' => [
            'livewireId' => $this->getId(),
            'name' => $this->getName(),
            'itemId' => data_get($item, $this->columnNameId),
        ],
    ];
@endphp
{{--If confirmation exists, use the javascript confirmation part ...--}}
@if(config('message-boxes.' . $jsMessageBoxDeleteItemPath))
    <button
            x-on:click="messageBox.show('{{ $jsMessageBoxDeleteItemPath }}', {{ json_encode($messageBoxParamsDelete) }} )"
            class="btn btn-sm btn-outline-danger btn-delete {{ data_get($this->mobileCssClasses, 'button', '') }}"
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
                x-on:click="messageBox.show('__default__.data-table.delete', {{ json_encode($messageBoxParamsDelete) }} )"
                class="btn btn-sm btn-outline-danger btn-delete {{ data_get($this->mobileCssClasses, 'button', '') }}"
                title="{{ __('Delete') }}"
        >
            <span class="bi bi-trash"></span>
        </button>
    @else
        <button
                wire:click="deleteItem('{{ $this->id }}', '{{ data_get($item, $this->columnNameId) }}')"
                class="btn btn-sm btn-danger btn-delete {{ data_get($this->mobileCssClasses, 'button', '') }}"
                title="{{ __('Delete') }}"
        >
            <span class="bi bi-trash"></span>
        </button>
    @endif

@endif
