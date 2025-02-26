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

    $timeLocale = $value ? Carbon::parse($value) : null;
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
    @endif
</div>
