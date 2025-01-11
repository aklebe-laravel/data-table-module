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
        class="{{ data_get($this->mobileCssClasses, 'button', '') }}">
    <span class="bi {{ $this->hasSelectedItem(data_get($item, $this->columnNameId)) ? 'text-success bi-check-square' : 'text-secondary bi-square' }}"></span>
</button>

