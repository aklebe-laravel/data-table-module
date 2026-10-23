@php
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /**
     * Use this by offers and cart items.
     * For products use attribute: price_formatted directly!
     *
     * @var BaseDataTable $this
     * @var Model $item
     * @var string $name
     * @var mixed $value
     **/

@endphp
<div class="text-muted-300">
    {{ number_format($value, 2) }}
</div>