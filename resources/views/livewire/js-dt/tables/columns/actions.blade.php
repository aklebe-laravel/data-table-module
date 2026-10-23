@php
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /**
     * @var BaseDataTable $this
     * @var Model $item
     * @var string $name
     * @var mixed $value
     **/
@endphp
<div class="whitespace-nowrap text-nowrap">
    @foreach($this->rowCommands as $commandView)
        @include($commandView)
    @endforeach
</div>
