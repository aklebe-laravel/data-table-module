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
@if(is_iterable($value))
    <span class="badge rounded-pill bg-white text-muted">
        {{ count($value) }}
    </span>
@else
    -
@endif
