@php
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;
    use Modules\SystemBase\app\Services\LivewireService;

    /**
     * @var BaseDataTable $this
     * @var Model $item
     * @var string $name
     * @var mixed $value
     **/
@endphp
<div class="d-flex float-end"
     wire:key="{{ LivewireService::getKey('action-'.data_get($item, $this->columnNameId)) }}">
    @foreach($this->rowCommands as $commandView)
        @include($commandView)
    @endforeach
</div>
