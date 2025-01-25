@php
    /** @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this */
    /** @var string $collectionName */
    /** @var array $_config */
@endphp
{{-- @todo: throttle/debounce value per settings --}}
{{-- @todo: throttle/debounce disable at enter key --}}
<input wire:model.live.debounce.750ms="filters.{{ $collectionName }}.{{ $_config['name'] }}" type="{{ $_config['type'] ?? 'text' }}" value=""
       class="form-control {{ $_config['css_item'] }} {{ $this->isFilterDefault($collectionName, 'search') ? '' : 'bg-warning-subtle' }}"
       placeholder="{{ __($_config['label']) }}"/>
