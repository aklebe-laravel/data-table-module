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
<x-lucide-columns-4 class="w-5 h-5 {{ $canDragAndDrop ? 'drag-handle cursor-move text-primary-500' : 'text-muted-300' }}"/>