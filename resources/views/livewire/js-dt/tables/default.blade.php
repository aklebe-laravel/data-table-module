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
    $xSortTag = '';
    if ($this->hasXSort) {
        $xSortTag = 'x-sort="Livewire.dispatchTo(\''.$this->getName().'\',\'x-sort-row\', {\'livewireId\':\''.$this->getId().'\',\'item\':$item,\'position\':$position})"';
    }
@endphp
<div class="table-wrapper border rounded p-1">

    <!-- Loading Overlay -->
    <div wire:loading.delay>
        @include('components.loading-overlay')
    </div>

    @if ($description || $descriptionView)
        <div class="container-fluid description">
            <div class="row">
                <div class="col-12 col-md">
                    @if ($description)
                        {!! __($description) !!}
                    @elseif ($descriptionView)
                        @include($descriptionView)
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if ($this->headerView)
        @include($this->headerView)
    @endif

    <div class="body table-responsive">
        <table class="table table-striped table-hover {{ $css_table ?? '' }}">
            <thead>
                <tr>
                    @foreach($allColumns as $column)
                        @if($this->getColumnParam($column, 'visible', true))
                            @php
                                $visibleColumnCount++;
                                $icon = $this->renderIcon($column);
                            @endphp
                            <th scope="col" class="{{ $column['css_all'] }} {{ $column['css_header'] }} text-nowrap"
                                title="{{ $column['label'] }}">
                            <span class="header-label"
                                  wire:click="$dispatchSelf('toggle-sort', {'column':'{{ $column['name'] }}', 'collectionName':'{{ $collectionName }}'})">
                                {!! $icon !!}
                                <span class="{{ $icon ? 'hide-mobile-show-lg' : '' }} decent">
                                    {{ Str::limit($column['label'], 5) }}
                                </span>
                            </span>
                                @if ($column['sortable'])
                                    <div class="header-sort-icons">
                                        <button
                                                class="header-sort-icon-up {{ $this->hasSort($column['name'], 'asc', $collectionName) ? 'text-danger' : 'text-secondary' }} "
                                                wire:click="$dispatchSelf('set-sort', {'column':'{{ $column['name'] }}', 'direction':'asc', 'collectionName':'{{ $collectionName }}'})"
                                        >
                                            <span class="bi bi-caret-up-fill"></span>
                                        </button>
                                        <button
                                                class="header-sort-icon-down {{ $this->hasSort($column['name'], 'desc', $collectionName) ? 'text-danger' : 'text-secondary' }} "
                                                wire:click="$dispatchSelf('set-sort', {'column':'{{ $column['name'] }}', 'direction':'desc', 'collectionName':'{{ $collectionName }}'})"
                                        >
                                            <span class="bi bi-caret-down-fill"></span>
                                        </button>
                                    </div>
                                @endif
                            </th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody {!! $xSortTag !!}>
            @if ($collection->count())
                @foreach($collection as $item)
                    @php
                        $_isItemValid = $this->isItemValid($item);
                        $_isItemWarn = $this->isItemWarn($item);
                    @endphp
                    <tr @if($xSortTag) x-sort:item="{{ data_get($item, 'id') }}" @endif class="">
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
                                <td class="{{ !$_isItemValid ? 'bg-danger-subtle' : ($_isItemWarn ? 'filtered' : '') }} {{ $column['css_all'] }} {{ $column['css_body'] }} dt-column-{{ $name }}">
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
