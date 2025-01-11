@php
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /**
     * @var BaseDataTable $this
     * @var Model         $item
     * @var string        $name
     * @var mixed         $value
     **/

    $_info1 = ($value instanceof Model) ? $value->toArray() : $value;
    $_info2 = ($item instanceof Model) ? $item->toArray() : $item;
@endphp
<div class="text-nowrap">
    <div><strong>{{ $name }}</strong></div>
    <pre>{{ print_r($_info1, true) }}</pre>
    <div><strong>{{ gettype($item) }}</strong></div>
    <pre>{{ print_r($_info2, true) }}</pre>
</div>