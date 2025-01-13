@php
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;
    use Modules\Form\app\Forms\Base\NativeObjectBase;

    /** @var BaseDataTable $this */
    /** @var string $collectionName */
    /** @var array $_config config for this data */

    $systemService = app('system_base');
    $htmlIdSuffix = $systemService->addUniqueCounter('switch3-id');
    $filterName = "filters.{$collectionName}.{$_config['name']}";
@endphp
<div class="switch3">
    <input wire:model.live.debounce="{{ $filterName }}" type="radio" id="switch3-radio1-{{ $htmlIdSuffix }}" value="{{ NativeObjectBase::switch3No }}"/>
    <label for="switch3-radio1-{{ $htmlIdSuffix }}"><span class="bi bi bi-hand-thumbs-down"></span></label>

    <input wire:model.live.debounce="{{ $filterName }}" type="radio" id="switch3-radio2-{{ $htmlIdSuffix }}"
           class="unused"
           value="{{ NativeObjectBase::switch3Unused }}"/>
    <label for="switch3-radio2-{{ $htmlIdSuffix }}" class="{{ $this->isFilterDefault($collectionName, $_config['name']) ? 'bg-light-subtle' : '' }}">{{ $_config['label'] }}</label>

    <input wire:model.live.debounce="{{ $filterName }}" type="radio" id="switch3-radio3-{{ $htmlIdSuffix }}" value="{{ NativeObjectBase::switch3Yes }}"/>
    <label for="switch3-radio3-{{ $htmlIdSuffix }}"><span class="bi bi bi-hand-thumbs-up"></span></label>
</div>
