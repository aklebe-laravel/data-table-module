@php
    /** @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this */
    /** @var string $collectionName */
@endphp
<select wire:model="filters.{{ $collectionName }}.rows"
        class="form-control {{ $_config['css_item'] }} {{ $this->isFilterDefault($collectionName, 'rows') ? '' : 'bg-warning-subtle' }}"
>
    @foreach(data_get($this, "filters.${collectionName}.rows_per_page", []) as $rpp)
        <option value="{{ $rpp }}">{{ __(':count Rows Max', ['count' => $rpp]) }}</option>
    @endforeach
</select>
