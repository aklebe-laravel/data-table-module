@php
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /**
     * @var BaseDataTable $this
     * @var Model $item
     * @var string $name
     * @var mixed $value
     **/
@endphp
<button wire:click="toggleSelectedItem('{{ data_get($item, $this->columnNameId) }}')" value="{{ data_get($item, $this->columnNameId) }}"
        class="dt-interaction {{ data_get($this->mobileCssClasses, 'button', '') }}">
    @if ($this->hasSelectedItem(data_get($item, $this->columnNameId)))
        <x-lucide-circle-check class="h-5 w-5 text-success-500"/>
    @else
        <x-lucide-circle class="h-5 w-5 text-secondary-500"/>
    @endif

</button>

