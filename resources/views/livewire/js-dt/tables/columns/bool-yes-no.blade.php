@php
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /**
     * @var BaseDataTable $this
     * @var Model $item
     * @var array $column
     * @var string $name
     * @var mixed $value
     **/
    if (data_get($column, 'options.negate', false)) {
        $value = !$value;
    }
@endphp
<div class="">
    <span class="bi {{ $value ? 'bi bi-check-lg' : '' }}"></span>
</div>

