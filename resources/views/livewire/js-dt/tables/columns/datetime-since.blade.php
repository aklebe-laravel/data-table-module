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

    if ($value) {
        $timeLocale = Carbon::parse($value)->locale('de');
    }
@endphp
<div class="text-muted">
    <div class="text-nowrap {{ ($value && ($timeLocale->diffInMinutes(Carbon::now()) <= 10)) ? 'text-success' : '' }}">
        @if ($value)
            {{ $timeLocale->shortRelativeToNowDiffForHumans() }}
        @else
            -
        @endif
    </div>
</div>
