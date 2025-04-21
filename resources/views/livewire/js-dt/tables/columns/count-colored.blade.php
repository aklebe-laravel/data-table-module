@php
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /**
     * @var BaseDataTable $this
     * @var Model $item
     * @var string $name
     * @var mixed $value
     **/
    $valueCount = count($value);
@endphp
@if(is_iterable($value))
    <span class="badge rounded-pill {{ $valueCount ? 'bg-success-subtle text-success' : '' }} text-muted">
        {{ $valueCount }}
    </span>
@else
    -
@endif
