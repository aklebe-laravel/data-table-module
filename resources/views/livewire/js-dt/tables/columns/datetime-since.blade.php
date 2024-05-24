@php
    /**
     * @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var \Illuminate\Database\Eloquent\Model $item
     * @var string $name
     * @var mixed $value
     **/

    use Illuminate\Support\Carbon;

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
