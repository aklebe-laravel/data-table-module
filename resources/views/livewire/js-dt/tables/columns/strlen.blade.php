@php
    /**
     * @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var Illuminate\Database\Eloquent\Model $item
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
