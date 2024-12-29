@php
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /**
     * @var BaseDataTable $this
     * @var Model $item
     * @var string $name
     * @var mixed $value
     * @var array $column
     **/
@endphp
{{ $value }}
{{--Check infos like observe_info_data.description to show "info exists" in a badge--}}
@foreach(data_get($column, 'options.observe_info_data', []) as $observeProperty)
    @if($_description = data_get($item, $observeProperty))
        <span class="p-0 m-0"
              data-bs-toggle="popover"
              data-bs-trigger="hover focus"
              title="{{ __('Existing Information') }}"
              data-bs-content="{{ Str::limit($_description, 200) }}">
            <span class="bi bi-info-circle text-warning"></span>
        </span>

    @endif
@endforeach
