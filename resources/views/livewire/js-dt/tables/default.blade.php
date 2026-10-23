{{-- This table will be included by several special views --}}
@php
    use Illuminate\Database\Eloquent\Collection;
    use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

    /** @var BaseDataTable $this */
    /** @var string $description */
    /** @var string $descriptionView */
    /** @var string $collectionName SHOULD come from outside */
    /** @var Collection $collection SHOULD come from outside */
    $collectionName = $collectionName ?? $this::COLLECTION_NAME_DEFAULT;
    $collection = $collection ?? $this->getCollection($collectionName);
    $allColumns = $this->getAllColumns();
    $visibleColumnCount = 0;
    $_index = 0;

    // true: if 'position' is the only sort column ...
    $canDragAndDrop = $this->canDragAndDrop($collectionName);
    $css_table ??= '';
@endphp
<div class="table-wrapper mb-8 {{ $css_table ? ('wrapper-'.$css_table) : 'border-1 border-surface-300/60' }}">

    <!-- Loading Overlay -->
    <div wire:loading.delay>
        @include('components.loading-overlay')
    </div>

    @if ($description || $descriptionView)
        <div class="description">
            @if ($description)
                {!! __($description) !!}
            @elseif ($descriptionView)
                @include($descriptionView)
            @endif
        </div>
    @endif

    @if ($this->headerView)
        @include($this->headerView)
    @endif

    <div class="body layout-table-responsive overflow-x-auto">
        <table class="table table-fixed table-hover border-collapse {{ $css_table }} w-full">
            <thead>
                <tr class="bg-surface-200/80 border-t-1 border-b-1 border-surface-300">
                    @foreach($allColumns as $column)
                        @if($this->getColumnParam($column, 'visible', true))
                            @php
                                $visibleColumnCount++;
                                $icon = 'lucide-' . ($column['icon'] ?? 'circle-question-mark');
                            @endphp
                            <th scope="col" class="px-5 py-3 {{ $column['css_header'] }} {{ $column['css_all'] }}"
                                title="{{ $column['label'] }}">

                                @include('data-table::livewire.js-dt.tables.columns.header.default-options')

                            </th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody wire:ignore.self
                   x-init="
                    @if(true)
                        new Sortable($el, {
                            handle: '.drag-handle',
                            animation: 150,
                            ghostClass: 'bg-blue-100', // Zeigt an, wo die Zeile landen würde
                            onEnd: (evt) => {
                                const movedId = evt.item.getAttribute('data-id');
                                const prevId = evt.item.previousElementSibling?.getAttribute('data-id') || null;
                                const nextId = evt.item.nextElementSibling?.getAttribute('data-id') || null;

                                // Wir senden die ID des bewegten Items und seine direkten Nachbarn
                                $wire.updateOrder('{{ $collectionName }}', movedId, prevId, nextId);
                            }
                        });
                    @endif
                ">
            @if ($collection->count())
                @foreach($collection as $item)
                    @php
                        $_isItemValid = $this->isItemValid($item);
                        $_isItemWarn = $this->isItemWarn($item);
                        $_strong = $_index++ % 2;
                    @endphp
                    <tr class="{{ $_strong ? 'bg-surface-200/20' : 'bg-surface-200/40' }} hover:bg-surface-300/40"
                        data-id="{{ data_get($item, 'id', 0) }}"
                        wire:key="row-{{ data_get($item, 'id', 0) }}"
                    >
                        @foreach($allColumns as $column)
                            @if($this->getColumnParam($column, 'visible', true))
                                @php
                                    $name = data_get($column, 'name', '');
                                    // get value by form declaration
                                    $value = data_get($column, 'value', '');
                                    if (app('system_base')->isCallableClosure($value)) {
                                        $value = $value($item);
                                    }
                                    // get value from object instance property name ...
                                    if (!$value) {
                                        $value = $name ? data_get($item, $name) : '';
                                    }
                                @endphp
                                <td class="px-5 py-3 {{ !$_isItemValid ? 'bg-danger-subtle' : ($_isItemWarn ? 'filtered' : '') }} {{ $column['css_all'] }} {{ $column['css_body'] }} dt-column-{{ $name }}">
                                    @include($column['view'] ?: 'data-table::livewire.js-dt.tables.columns.default')
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="{{ $visibleColumnCount }}">
                        <div class="empty-list text-muted">
                            {{ __('No entries found.') }}
                        </div>
                    </td>
                </tr>
            @endif
            </tbody>
            <tfoot>
            <tr>
                @foreach($allColumns as $column)
                    @if($this->getColumnParam($column, 'visible', true))
                        <td class="{{ $column['css_all'] }} {{ $column['css_footer'] }}">
                            <span class="decent"></span>
                        </td>
                    @endif
                @endforeach
            </tr>
            </tfoot>
        </table>

    </div>

    @if ($this->footerView)
        @include($this->footerView)
    @endif

</div>
