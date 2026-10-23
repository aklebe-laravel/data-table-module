@php
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /** @var BaseDataTable $this */
    /** @var string $collectionName */
    /** @var array $_config config for this data */
    $attrs = [
        'class' => $_config['css_item']. ' ' . ($this->isFilterDefault($collectionName, $_config['name']) ? '' : 'filtered'),
        'wire:model.live.debounce' => "filters.$collectionName.{$_config['name']}",
    ];
    $attrs = new \Illuminate\View\ComponentAttributeBag($attrs);
    //$value = $data['value'];
    $options = collect($_config['options'])->map(fn($label, $value) => [
        'label' => $label,
        'value' => $value
    ])->values()->toArray();
@endphp
<x-select.styled :attributes="$attrs" :options="$options" />