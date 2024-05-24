@php
    /**
     * @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var \Illuminate\Database\Eloquent\Model $item
     * @var string $name
     * @var mixed $value
     **/

    $jsMessageBoxSimulateItemPath = \Illuminate\Support\Str::snake($this->getModelName(), '-') . '.data-table.simulate-item';
    $jsMessageBoxLaunchItemPath = \Illuminate\Support\Str::snake($this->getModelName(), '-') . '.data-table.launch-item';

    $objName = $this->getName();

    if ($this->renderMode !== $this::RENDER_MODE_BACKEND) {
        return;
    }
@endphp
<button
        class="btn btn-sm btn-outline-success mr-2 {{ data_get($this->mobileCssClasses, 'button', '') }}"
        x-on:click="messageBox.show('{{ $jsMessageBoxSimulateItemPath }}', {'simulate-item': {livewire_id: '{{ $this->getId() }}', name: '{{ $objName }}', item_id: {{ data_get($item, 'id') }}}})"
        title="{{ __('Simulate Item') }}"
>
    <span class="bi bi-play-fill"></span>
</button>
