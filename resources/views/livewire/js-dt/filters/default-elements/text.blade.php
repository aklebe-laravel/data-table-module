@php
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /** @var BaseDataTable $this */
    /** @var string $collectionName */
    /** @var array $_config */
    $attrs = [
        'class' => $_config['css_item']. ' ' . ($this->isFilterDefault($collectionName, $_config['name']) ? '' : 'filtered'),
        'wire:model.live.debounce.750ms' => "filters.$collectionName.{$_config['name']}",
        'placeholder' =>__($_config['label']),
    ];
    $attrs = new \Illuminate\View\ComponentAttributeBag($attrs);
@endphp
{{-- @todo: throttle/debounce value per settings --}}
{{-- @todo: throttle/debounce disable at enter key --}}
<x-input :attributes="$attrs" />