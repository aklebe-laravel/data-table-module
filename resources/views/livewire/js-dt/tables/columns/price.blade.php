@php
    /**
     * Use this by offers and cart items.
     * For products use attribute: price_formatted directly!
     *
     * @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var \Illuminate\Database\Eloquent\Model $item
     * @var string $name
     * @var mixed $value
     **/
    $currency = data_get($item, 'currency_code', data_get($item, 'currency', ''));
    $paymentMethod = data_get($item, 'paymentMethod.code', '');
@endphp
<div class="">
    {{ app('system_base')->getPriceFormatted($value, $currency, $paymentMethod) }}
</div>

