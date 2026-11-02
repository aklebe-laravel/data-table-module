{{-- Default card of TABLE_VIEW_CARDS: all visible columns with their column views, actions at the bottom --}}
@php
    use Illuminate\Database\Eloquent\Model;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /** @var BaseDataTable $this */
    /** @var Model $item */
    /** @var string $collectionName */
    $_isItemValid = $this->isItemValid($item);
    $_isItemWarn = $this->isItemWarn($item);
    $_itemId = data_get($item, $this->columnNameId, 0);
@endphp
<div data-id="{{ $_itemId }}"
     wire:key="card-{{ $_itemId }}"
        @class([
            'flex h-full flex-col gap-2 rounded-xl border p-3 transition-colors',
            'border-surface-200 bg-surface-50 hover:border-surface-400' => $_isItemValid,
            'border-danger-500 bg-danger-500/10' => !$_isItemValid,
            'filtered' => $_isItemWarn,
        ])>
    @foreach($this->getAllColumns() as $column)
        @if($this->getColumnParam($column, 'visible', true))
            @php
                $name = data_get($column, 'name', '');
                $value = $this->getColumnValue($column, $item);
                $isActions = ($name === 'actions');
            @endphp
            {{-- css_all holds table widths and breakpoints, cards use css_body only --}}
            <div @class(['dt-column-'.$name, $column['css_body'], 'mt-auto pt-2 border-t border-surface-200 text-end' => $isActions])>
                @if(!$isActions && data_get($column, 'options.card_label', true))
                    <div class="text-xs text-surface-500">{{ $column['label'] }}</div>
                @endif
                @include($column['view'] ?: 'data-table::livewire.js-dt.tables.columns.default')
            </div>
        @endif
    @endforeach
</div>
