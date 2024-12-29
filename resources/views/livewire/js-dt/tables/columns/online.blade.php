@php
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Carbon;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /**
     * @var BaseDataTable $this
     * @var Model $item
     * @var string $name
     * @var mixed $value
     **/

    if ($value !== null && !is_int($value) && !is_bool($value)) {
        $date = Carbon::parse($value);
        $now = Carbon::now();
        $diff = $date->diffInMinutes($now);
        $value = ($diff < 2);
    }
@endphp
<div class="">
    <span class="bi {{ $value ? 'text-success bi bi-check-circle-fill' : 'text-danger bi bi-x-circle-fill' }}"></span>
</div>

