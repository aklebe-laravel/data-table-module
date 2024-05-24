@php
    /**
     * @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var Illuminate\Database\Eloquent\Model $item
     * @var string $name
     * @var mixed $value
     **/
    $_contentInKb = ceil(strlen($value) / 1024);
@endphp
@if(is_string($value))
    <span class="badge rounded-pill {{ ($_contentInKb) ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
        {{ $_contentInKb }} kb
    </span>
@else
    -
@endif
