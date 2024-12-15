@php
    /**
     * @var BaseDataTable $this
     * @var Model $item
     * @var string $name
     * @var mixed $value
     **/
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;
@endphp
@if(is_string($value))
    <span class="badge rounded-pill {{ (strlen($value)) ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
        {{ app('system_base')->bytesToHuman(strlen($value)) }}
    </span>
@else
    -
@endif
