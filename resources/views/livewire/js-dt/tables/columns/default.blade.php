@php
    /**
     * @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var Illuminate\Database\Eloquent\Model $item
     * @var string $name
     * @var mixed $value
     * @var array $column
     **/

    $origValue = $value;
    $value = $column['translation'] ? __($value) : $value;
    if ($strLimit = data_get($column, 'options.str_limit', 0)) {
        $value = Str::limit($value, $strLimit);
    }
@endphp
<div class="">
    @if (data_get($column, 'options.has_open_link', false))
        {{--        @if ($this->renderMode == $this::RENDER_MODE_BACKEND)--}}
        @if($this->editable && $this->relatedLivewireForm)
            <button
                    class="btn-link link-primary text-start"
                    wire:click="$dispatchTo('{{ $this->relatedLivewireForm }}', 'open-form', {id: {{ data_get($item, 'id') }} })"
            >
                @include("data-table::livewire.js-dt.tables.columns.value-with-info")
            </button>
        @else
            {{ $value }}
        @endif
        {{--        @else--}}
        {{--            <a href="{{ route('product', $item->web_uri) }}" target="_blank">{{ $value }}</a>--}}
        {{--        @endif--}}
    @else
        {{ $value }}
    @endif
    @foreach(data_get($column, 'options.popups', []) as $popup)
        @if($popupContent = data_get($popup, 'content', $origValue))
            <span class="p-0 m-0"
                  data-bs-toggle="popover"
                  data-bs-trigger="hover focus"
                  title="{{ data_get($popup, 'title', data_get($column, 'label', '?')) }}"
                  data-bs-content="{!! $popupContent !!}">

                {{--Label istead of an icon?--}}
                @if($popupLabel = data_get($popup, 'label'))
                    <span class="badge bg-secondary cursor-pointer">{{ $popupLabel }}</span>
                @else
                    <span class="text-secondary cursor-pointer {{ data_get($popup, 'icon', 'bi bi-info-circle') }}"></span>
                @endif

        </span>
        @endif
    @endforeach
</div>

