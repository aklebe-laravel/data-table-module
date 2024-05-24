@php
    /**
     * @var \Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var Illuminate\Database\Eloquent\Model $item
     * @var string $name
     * @var mixed $value
     **/
@endphp
<div class="d-flex float-end"
     wire:key="{{ \Modules\SystemBase\app\Services\LivewireService::getKey('action-'.data_get($item, 'id')) }}">
    @foreach($this->rowCommands as $commandView)
        @include($commandView)
    @endforeach
</div>
