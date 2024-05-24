@php
    /**
     * @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var Illuminate\Database\Eloquent\Model $item
     * @var string $name
     * @var mixed $value
     **/
    $_info1 = ($value instanceof \Illuminate\Database\Eloquent\Model) ? $value->toArray() : $value;
    $_info2 = ($item instanceof \Illuminate\Database\Eloquent\Model) ? $item->toArray() : $item;
@endphp
<div class="text-nowrap">
    {{--    {{ dump($item) }}--}}
    <div><strong>{{ $name }}</strong></div>
    <pre>{{ print_r($_info1) }}</pre>
    <div><strong>{{ gettype($item) }}</strong></div>
    <pre>{{ print_r($_info2) }}</pre>
</div>