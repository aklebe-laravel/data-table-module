@php
    /**
     * @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var Illuminate\Database\Eloquent\Model $item
     * @var string $name
     * @var mixed $value
     **/
    $timeLocale = $value ? \Illuminate\Support\Carbon::parse($value)->locale('de') : null;
@endphp
<div class="text-muted">
    @if ($timeLocale)
        <div class="text-nowrap">
            {{ $timeLocale->dayName }}, {{ $timeLocale->translatedFormat('d.m.Y') }}
        </div>
        <div class="text-nowrap">
            {{ $timeLocale->translatedFormat('H:i') }}h ({{ $timeLocale->shortAbsoluteDiffForHumans() }})
        </div>
    @else
        -
        {{--        <button class="btn btn-outline-secondary disabled">--}}
        {{--            <span class="bi bi-infinity"></span>--}}
        {{--        </button>--}}
    @endif
</div>
