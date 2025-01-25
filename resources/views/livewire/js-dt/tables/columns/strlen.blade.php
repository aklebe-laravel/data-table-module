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
@if(is_string($value))
    <span class="badge rounded-pill bg-secondary">
        {{ strlen($value) }}
    </span>
@else
    -
@endif
