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

    $currency = data_get($item, 'currency_code', data_get($item, 'currency', ''));
    $paymentMethod = data_get($item, 'paymentMethod.code', '');
@endphp
<div class="">
    {{ app('system_base')->getPriceFormatted($value, $currency, $paymentMethod) }}
</div>

