@php
    /** @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this */
    /** @var string $collectionName */
@endphp
<div class="col">
    <select class="form-control">
        <option value="">{{ __('Select ...') }}</option>
        <option value="export">{{ __('Export ...') }}</option>
        <option value="print">{{ __('Print ...') }}</option>
    </select>
</div>
