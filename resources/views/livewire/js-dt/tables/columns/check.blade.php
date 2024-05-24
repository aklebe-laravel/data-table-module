@php
    /**
     * @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var \Illuminate\Database\Eloquent\Model $item
     * @var string $name
     * @var mixed $value
     **/
@endphp
{{--    <input wire:model="selectedItems" value="{{ $item->id }}" class="form-check-input" type="checkbox" role="switch">--}}
<button wire:click="toggleSelectedItem({{ data_get($item, 'id') }})" value="{{ data_get($item, 'id') }}"
        class="{{ data_get($this->mobileCssClasses, 'button', '') }}">
    <span class="bi {{ $this->hasSelectedItem(data_get($item, 'id')) ? 'text-success bi-check-square' : 'text-secondary bi-square' }}"></span>
</button>

