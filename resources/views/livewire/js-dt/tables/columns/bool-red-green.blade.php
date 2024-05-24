@php
    /**
     * @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var \Illuminate\Database\Eloquent\Model $item
     * @var array $column
     * @var string $name
     * @var mixed $value
     **/
    if (data_get($column, 'options.negate', false)) {
        $value = !$value;
    }
@endphp
<div class="">
    <span class="bi {{ $value ? 'text-success bi bi-check-circle-fill' : 'text-danger bi bi-x-circle-fill' }}"></span>
</div>

