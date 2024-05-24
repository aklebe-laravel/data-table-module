@php
    /**
     * @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var \Illuminate\Database\Eloquent\Model $item
     * @var string $name
     * @var mixed $value
     **/
    if ($value !== null && !is_int($value) && !is_bool($value)) {
        $date = \Illuminate\Support\Carbon::parse($value);
        $now = \Illuminate\Support\Carbon::now();
        $diff = $date->diffInMinutes($now);
        $value = ($diff < 2);
    }
@endphp
<div class="">
    <span class="bi {{ $value ? 'text-success bi bi-check-circle-fill' : 'text-danger bi bi-x-circle-fill' }}"></span>
</div>

