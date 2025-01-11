@php
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /**
     * @var BaseDataTable $this
     * @var Model $item
     * @var string $name
     * @var mixed $value
     **/

    $jsMessageBoxSimulateItemPath = \Illuminate\Support\Str::snake($this->getEloquentModelName(), '-') . '.data-table.simulate-item';
    $jsMessageBoxLaunchItemPath = \Illuminate\Support\Str::snake($this->getEloquentModelName(), '-') . '.data-table.launch-item';

    $objName = $this->getName();

    if ($this->renderMode !== $this::RENDER_MODE_BACKEND) {
        return;
    }

    $messageBoxParams1 = [
        'launch-item' => [
            'livewireId' => $this->getId(),
            'name' => $objName,
            'itemId' => data_get($item, $this->columnNameId),
        ],
    ];
@endphp
<button
        class="btn btn-sm btn-outline-danger mr-2 {{ data_get($this->mobileCssClasses, 'button', '') }}"
        x-on:click="messageBox.show('{{ $jsMessageBoxLaunchItemPath }}', {{ json_encode($messageBoxParams1) }} )"
        title="{{ __('Launch Item') }}"
>
    <span class="bi bi-play-fill"></span>
</button>

