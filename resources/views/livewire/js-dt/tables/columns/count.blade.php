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
    <span class="badge rounded-pill {{ (count($value)) ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
        {{ count($value) }}
    </span>
@else
    -
@endif
