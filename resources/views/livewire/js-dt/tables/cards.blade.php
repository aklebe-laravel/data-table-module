{{-- Cards in a grid instead of table rows, see BaseDataTable::$tableView. Included like tables/default.blade.php --}}
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
    $sortColumns = $this->getCardSortColumns();
    $css_table ??= '';
@endphp
<div class="table-wrapper {{ $css_table ? ('mb-8 wrapper-'.$css_table) : $this->cardsWrapperCss }}">

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

    {{-- no table header here: sortable columns as buttons --}}
    @if ($sortColumns)
        <div class="flex flex-wrap items-center justify-end gap-2 px-4 pt-3 text-sm">
            <span class="text-surface-500">{{ __('Sort by') }}:</span>
            @foreach($sortColumns as $column)
                @php
                    $direction = $this->getSortDirection($collectionName, $column['name']);
                @endphp
                <button type="button"
                        wire:click="setSingleSort('{{ $column['name'] }}', '{{ $collectionName }}')"
                        @class([
                            'inline-flex items-center gap-1 px-3 py-1 rounded-lg border transition-colors cursor-pointer',
                            'border-primary-500 bg-primary-50/50 text-primary-700' => $direction,
                            'border-surface-300 text-surface-600 hover:border-surface-400' => !$direction,
                        ])>
                    {{ $column['label'] }}
                    @if ($direction === 'asc')
                        <x-lucide-arrow-up class="w-3.5 h-3.5"/>
                    @elseif ($direction === 'desc')
                        <x-lucide-arrow-down class="w-3.5 h-3.5"/>
                    @endif
                </button>
            @endforeach
        </div>
    @endif

    <div class="body {{ $this->cardsBodyCss }}">
        @if ($collection->count())
            <div class="grid {{ $this->cardsGridCss }}">
                @foreach($collection as $item)
                    @include($this->cardView)
                @endforeach
            </div>
        @else
            <div class="empty-list text-muted text-center py-6">
                {{ __('No entries found.') }}
            </div>
        @endif
    </div>

    @if ($this->footerView)
        @include($this->footerView)
    @endif

</div>
