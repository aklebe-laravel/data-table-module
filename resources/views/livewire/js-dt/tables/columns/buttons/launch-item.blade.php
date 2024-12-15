@php
    /**
     * @var BaseDataTable $this
     * @var Model $item
     * @var string $name
     * @var mixed $value
     **/

    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    $jsMessageBoxSimulateItemPath = \Illuminate\Support\Str::snake($this->getEloquentModelName(), '-') . '.data-table.simulate-item';
    $jsMessageBoxLaunchItemPath = \Illuminate\Support\Str::snake($this->getEloquentModelName(), '-') . '.data-table.launch-item';

    $objName = $this->getName();

    if ($this->renderMode !== $this::RENDER_MODE_BACKEND) {
        return;
    }
@endphp
<button
        class="btn btn-sm btn-outline-danger mr-2 {{ data_get($this->mobileCssClasses, 'button', '') }}"
        x-on:click="messageBox.show('{{ $jsMessageBoxLaunchItemPath }}', {'launch-item': {livewire_id: '{{ $this->getId() }}', name: '{{ $objName }}', item_id: {{ data_get($item, 'id') }}}})"
        title="{{ __('Launch Item') }}"
>
    <span class="bi bi-play-fill"></span>
</button>

