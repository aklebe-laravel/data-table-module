<?php

namespace Modules\DataTable\app\Http\Livewire\DataTable\Base;

use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Modules\Acl\app\Models\AclResource;
use Modules\Acl\app\Services\UserService;
use Modules\SystemBase\app\Http\Livewire\BaseComponent;
use Modules\SystemBase\app\Services\CacheService;
use Modules\SystemBase\app\Services\ModelService;
use Modules\SystemBase\app\Traits\TraitModelAddMeta;

/**
 *
 */
class BaseDataTable extends BaseComponent
{
    /**
     * Minimum restrictions to allow this component.
     */
    public const array aclResources = [AclResource::RES_DEVELOPER, AclResource::RES_ADMIN];

    /**
     *
     */
    const string COLLECTION_NAME_DEFAULT = 'default';

    /**
     *
     */
    const string COLLECTION_NAME_SELECTED_ITEMS = 'selected_items';

    /**
     *
     */
    const string COLLECTION_NAME_UNSELECTED_ITEMS = 'unselected_items';

    /**
     *
     */
    const string RENDER_MODE_FRONTEND = 'FRONTEND';

    /**
     *
     */
    const string RENDER_MODE_BACKEND = 'BACKEND';

    /**
     * Relations loaded with getBaseBuilder()
     * This is also important to lo load pivots automatically.
     *
     * @var array
     */
    public array $objectRelations = [];

    /**
     * @var string
     */
    public string $columnNameId = 'id';

    /**
     * Overwrite this to be able to filter by users.
     *
     * @var string
     */
    public string $columnUserId = ''; // 'user_id'

    /**
     * Overwrite this to be able to order rows by this value by default.
     *
     * @var string
     */
    public string $columnPosition = ''; // 'position'

    /**
     * Decides behaviour: For example: edit products in form (default) or link to product view (in search).
     *
     * @var string
     */
    public string $renderMode = self::RENDER_MODE_BACKEND;

    /**
     * Title for this datatable. Usually set from view as parameter
     * and changed as livewire parameter.
     *
     * @var string
     */
    public string $title = '';

    /**
     * @var string
     */
    public string $description = ''; // 'Base description ... should be removed!';

    /**
     * @var string
     */
    public string $descriptionView = '';

    /**
     * Enable/disable editable actions.
     * Usually changed as livewire parameter.
     *
     * @var bool
     */
    public bool $editable = true;

    /**
     * Adjust the row results for the current frontend user instead of a non-user view like a managed view.
     *
     * getBaseBuilder() should be overwritten and check this property
     * to prepare for owner objects only.
     *
     * @var bool
     */
    public bool $filterByParentOwner = true;

    /**
     * Enable/disable delete actions.
     * Usually changed as livewire parameter.
     *
     * @var bool
     */
    public bool $removable = true;

    /**
     * Enables checkbox and bulk actions.
     * Default false for all datatables.
     * Should be enabled if [@see self::parentRelationMethodForThisBuilder} exist.
     *
     * @var bool
     */
    public bool $selectable = false;

    /**
     * Determine whether command has buttons like "add new row" in header.
     *
     * @var bool
     */
    public bool $canAddRow = true;

    /**
     * Determine whether command has buttons like "add new row" in header.
     * Usually changed as livewire parameter.
     *
     * @var bool
     */
    public bool $hasCommands = true;

    /**
     * Here is the definition of all possible buttons.
     * The row command views itself have to check to allow the button.
     *
     * @var array|string[]
     */
    protected array $rowCommands = [];

    /**
     * Collection items. Used for all collection names at once.
     * Usually changed as livewire parameter.
     *
     * @var array
     */
    public array $selectedItems = [];

    /**
     * If given, it's the relation method of a model.
     * Ex: "categories" if you want present the data table for $product->categories()
     * Usually changed as livewire parameter.
     *
     * @var string
     */
    public string $parentRelationMethodForThisBuilder = '';

    /**
     * If given, it's the related form where this form is used to edit their items.
     * Used for rows edit button.
     * Usually changed as livewire parameter.
     *
     * @var string
     */
    public string $relatedLivewireForm = '';

    /**
     * Can be overwritten and should if class names differ.
     *
     * @var string
     */
    public string $eloquentModelName = '';

    /**
     * Not presented names will not be rendered.
     * see resources/views/livewire/js-dt/dt-auto.blade.php
     *
     * @var array|true[]
     */
    public array $enabledCollectionNames = [
        self::COLLECTION_NAME_DEFAULT          => true,
        self::COLLECTION_NAME_SELECTED_ITEMS   => true,
        self::COLLECTION_NAME_UNSELECTED_ITEMS => true,
    ];

    /**
     * Leave empty to hide the header.
     *
     * @var string
     */
    public string $headerView = 'data-table::livewire.js-dt.tables.headers.default';

    /**
     *
     * @var array|array[]
     */
    protected array $filterElementConfig = [];

    /**
     * Filter data root indexed by collection name
     *
     * @var array
     */
    public array $filters = [];

    /**
     * Leave empty to hide the footer.
     *
     * @var string
     */
    public string $footerView = 'data-table::livewire.js-dt.tables.footers.default';

    /**
     * @var string
     */
    public string $footerActions = '';

    /**
     * Collections root indexed by collection name
     *
     * @var Collection[]
     */
    protected array $collections = [];

    /**
     * Enable this for data tables without an eloquent model (getFixCollection() returns the rows).
     * The rows then get search, filters, sorting and pagination like a builder based table:
     * - search runs in memory over all 'searchable' columns
     * - custom filter elements need a 'collection_builder' callback (instead of 'builder'):
     *   function (SupportCollection $items, string $filterElementKey, mixed $filterValue): SupportCollection
     * - selected/unselected collections are resolved by $columnNameId
     *
     * Disabled by default, because existing tables may already prepare their fix collection by themselves.
     *
     * @var bool
     */
    protected bool $processFixCollection = false;

    /**
     * Rows of getFixCollection() per collection name, memoized for the current request only.
     * Header, footer and table call it several times per render (count, pagination, rows).
     *
     * @var array<string, SupportCollection|null>
     */
    protected array $fixCollectionCache = [];

    /**
     * @var array
     */
    protected array $columnDefaults = [
        'name'        => '',
        'value'       => '',
        'label'       => '???',
        'searchable'  => false,
        'sortable'    => false,
        'visible'     => true,
        // CSS column content
        'css_body'    => '',

        // CSS Header (for th only):
        // Header will be rendered together with css_all
        // Set Columns-Width in Header! For best width control wit tailwind:
        // - table: "table-fixed w-full"
        // - th the smallest width: "w-px whitespace-nowrap"
        // - th the biggest width: no with or "w-auto"
        // - th fixed width: "w-12 truncate" or "w-50 overflow-hidden"
        // - th use percent from left space width: "w-[70%] truncate" or "w-[30%] overflow-hidden"
        'css_header'  => '',

        // CSS for th and td
        'css_all'     => '', // 'w-fit text-start',

        // CSS header, footer and content
        'css_footer'  => '',
        // 'number', 'timestamp', ...
        'format'      => '',
        // custom view path for content
        'view'        => '',
        // without view only. If true, use __(...) for values
        'translation' => false,
        // column specific ...
        'options'     => [
            //'has_open_link' => bool, // clickable link to edit form
            //'has_frontend_link' => bool, // clickable link to frontend view (works only when has_open_link is false)
            //'str_limit' => int, // max chars to display
        ],
    ];

    /**
     * cached from getAllColumns() to avoid unnecessary calculations
     *
     * @var array
     */
    protected array $allColumnsCached = [];

    /**
     * @var array|string[]
     */
    protected array $mobileCssClasses = [
        'button' => 'm-0 ms-1 m-md-1 p-0 p-sm-1 p-md-2',
    ];

    /**
     * Runs once, immediately after the component is instantiated, but before render() is called.
     * This is only called once on initial page load and never called again, even on component refreshes
     *
     * @return void
     */
    protected function initMount(): void
    {
        parent::initMount();
    }

    /**
     * Runs on every request, after the component is mounted or hydrated, but before any update methods are called
     *
     * @return void
     */
    protected function initBooted(): void
    {
        parent::initBooted();

        $this->initFilters();

        $this->getFiltersSession();

        if ($this->canManage()) {
            $this->rowCommands = array_merge($this->rowCommands, [
                'duplicate' => 'data-table::livewire.js-dt.tables.columns.buttons.duplicate',
                'edit'      => 'data-table::livewire.js-dt.tables.columns.buttons.edit',
                'delete'    => 'data-table::livewire.js-dt.tables.columns.buttons.delete',
            ]);
        }

        $this->addMessageBoxes();
    }

    /**
     * Add messagebox buttons and call it in initBooted()
     *
     * @return void
     */
    protected function addMessageBoxes(): void
    {
        // @todo: 'data-table' is messed ... more performant is to let similar dts decide
        app('system_base')->provideMessageBoxButtons(category: 'data-table');
    }

    /**
     * Overwrite to init your sort orders before session exists
     *
     * @return void
     */
    protected function initSort(): void
    {
        // $this->setSortAllCollections('updated_at', 'desc');
    }

    /**
     * Should be overridden if more filters needed or existing filters not wanted!
     *
     * @return void
     */
    protected function initFilters(): void
    {
        $this->filterElementConfig = [];

        $this->addFilterElement('rows_per_page', [
            'label'      => 'Rows',
            'default'    => ($this->parentData) ? 8 : 50, // prevent mess up in sub forms
            'position'   => 1000,
            'soft_reset' => true,
            'css_item'   => '',
            'options'    => ($this->parentData)  // prevent mess up in sub forms
                ? [
                    8  => 8,
                    15 => 15,
                    25 => 25,
                ]
                : [
                    10  => 10,
                    20  => 20,
                    30  => 30,
                    50  => 50,
                    100 => 100,
                    200 => 200,
                    500 => 500,
                ],
            'builder'    => null, // rows_per_page evaluated in main process
            'view'       => 'data-table::livewire.js-dt.filters.default-elements.select',
        ]);
        $this->addFilterElement('search',
            [
                'label'              => 'Search ...',
                'default'            => '',
                'position'           => 2000,
                'soft_reset'         => true,
                'css_item'           => '',
                'builder'            => function (Builder $builder, string $filterElementKey, string $filterValue) {
                    if (!$filterValue) {
                        return;
                    }
                    $searchLike = '%'.$filterValue.'%';
                    $builder->where(function (Builder $b) use ($searchLike) {
                        foreach ($this->getAllColumns() as $column) {
                            if ($columnName = data_get($column, 'name')) {
                                if (data_get($column, 'searchable', true)) {

                                    // perform things like "pivot.position>100"
                                    app(ModelService::class)->resolveDotsForWhere($b, $columnName, $searchLike, 'like');
                                }
                            }
                        }
                    });
                },
                // used for fix collections (see $processFixCollection)
                'collection_builder' => function (SupportCollection $items, string $filterElementKey, mixed $filterValue): SupportCollection {
                    $needle = mb_strtolower(trim((string) $filterValue));
                    if ($needle === '') {
                        return $items;
                    }

                    $searchColumns = [];
                    foreach ($this->getAllColumns() as $column) {
                        if (($columnName = data_get($column, 'name')) && data_get($column, 'searchable', true)) {
                            $searchColumns[] = $columnName;
                        }
                    }

                    return $items->filter(function ($item) use ($searchColumns, $needle) {
                        foreach ($searchColumns as $columnName) {
                            if (str_contains(mb_strtolower($this->stringifyForSearch(data_get($item, $columnName))), $needle)) {
                                return true;
                            }
                        }

                        return false;
                    });
                },
                'view'               => 'data-table::livewire.js-dt.filters.default-elements.search-and-settings',
            ]);
    }

    /**
     * Flat string of a cell value for the in-memory search.
     *
     * @param  mixed  $value
     *
     * @return string
     */
    protected function stringifyForSearch(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_scalar($value)) {
            return (string) $value;
        }
        if (is_array($value) || $value instanceof SupportCollection) {
            return implode(' ', array_map(fn($v) => is_scalar($v) ? (string) $v : '', Arr::flatten(collect($value)->toArray())));
        }
        if ($value instanceof \Stringable) {
            return (string) $value;
        }

        return '';
    }

    /**
     * @param  string  $name
     * @param  array   $data
     *
     * @return void
     */
    protected function addFilterElement(string $name, array $data): void
    {
        $this->filterElementConfig[$name] = $data;
        $this->filterElementConfig[$name]['name'] = $name;
    }

    /**
     * @param  string  $name
     *
     * @return void
     */
    protected function removeFilterElement(string $name): void
    {
        if (isset($this->filterElementConfig[$name])) {
            unset($this->filterElementConfig[$name]);
        }
    }

    /**
     * @return array
     */
    protected function getOrderedFilterElementConfig(): array
    {
        return collect($this->filterElementConfig)
            ->sortBy('position')
            ->toArray();
    }

    /**
     * @return string
     */
    protected function getFiltersSessionName(): string
    {
        return 'dt_filter'.$this->getName();
    }

    /**
     * @return bool
     */
    protected function getFiltersSession(): bool
    {
        if ($v = Session::get($this->getFiltersSessionName())) {
            $this->filters = app('system_base')->arrayMergeRecursiveDistinct($this->filters, $v);

            return true;
        }

        //$this->filters = $this->filterValueDefaults;
        $this->setFilterDefaultValues();

        $this->initSort();

        return false;
    }

    /**
     * @return void
     */
    protected function setFilterDefaultValues(): void
    {
        $this->filters = [
            self::COLLECTION_NAME_DEFAULT          => [],
            self::COLLECTION_NAME_SELECTED_ITEMS   => [],
            self::COLLECTION_NAME_UNSELECTED_ITEMS => [],
        ];

        foreach ($this->filterElementConfig as $name => $config) {
            $v = data_get($config, 'default');
            $this->filters[self::COLLECTION_NAME_DEFAULT][$name] = $v;
            $this->filters[self::COLLECTION_NAME_SELECTED_ITEMS][$name] = $v;
            $this->filters[self::COLLECTION_NAME_UNSELECTED_ITEMS][$name] = $v;
        }
    }

    /**
     * @return void
     */
    protected function updateFiltersSession(): void
    {
        Session::put($this->getFiltersSessionName(), $this->filters);
    }

    /**
     * @return void
     */
    protected function resetFiltersSession(): void
    {
        Session::forget($this->getFiltersSessionName());
    }

    /**
     * @param  string  $collectionName
     * @param  string  $keyPath
     *
     * @return bool
     */
    public function isFilterDefault(string $collectionName, string $keyPath): bool
    {
        $filterKey = $collectionName.'.'.$keyPath;

        // !important to use "==" instead of "===" here because scalar types can differ!
        return (data_get($this->filters, $filterKey) == data_get($this->filterElementConfig, $keyPath.'.default'));
    }

    /**
     * Should be overwritten if this datatable is about s specific user.
     * For example list products of another user.
     *
     * @return int|string|null
     */
    public function getUserId(): int|string|null
    {
        if (($parentModelId = data_get($this->parentData, 'id', '')) && ($parentModelName = data_get($this->parentData, 'model_class', ''))) {
            if ($parentModel = $parentModelName::with([])
                ->find($parentModelId)
            ) {
                if ($parentModel->user_id ?? null) {
                    return $parentModel->user_id;
                }
                if (($parentModel->user ?? null) && ($parentModel->user->id ?? null)) {
                    return $parentModel->user->id;
                }
                if ($parentModel instanceof User) {
                    return $parentModel->getKey();
                }
            }
        }

        return Auth::id();
    }

    /**
     * Should be overwritten if this datatable is about s specific user.
     * For example list products of another user.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        if (!($id = $this->getUserId())) {
            return app('user')
                ->with([])
                ->find($id);
        } else {
            // @todo: cache or component public property
            return app('user')
                ->with([])
                ->find($id);
        }
    }

    /**
     * Should be overwritten to decide the current object is owned by user
     * canEdit() can call canManage() but don't call canEdit() in canManage()!
     *
     * @return bool
     */
    public function canEdit(): bool
    {
        return $this->editable;
    }

    /**
     * Should be overwritten to decide the current object is owned by user
     * canEdit() can call canManage() but don't call canEdit() in canManage()!
     *
     * @return bool
     */
    public function canManage(): bool
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);

        return $userService->currentUser()
            ->hasAclResource(AclResource::RES_MANAGE_USERS);
    }

    /**
     * Should be overwritten to define columns.
     *
     * @return array[]
     */
    public function getColumns(): array
    {
        return [
            [
                'name'   => 'id',
                'label'  => 'ID',
                'format' => 'number',
            ],
            [
                'name'  => 'name',
                'label' => 'Name',
            ],
            [
                'name'  => 'description',
                'label' => 'Description',
            ],
        ];
    }

    /**
     * @param  string  $name
     *
     * @return array
     */
    public function getColumnByName(string $name): array
    {
        //        foreach ($this->getColumns() as $column) {
        foreach ($this->getAllColumns() as $column) {
            if ((Arr::get($column, 'name')) === $name) {
                return $column;
            }
        }

        return [];
    }

    /**
     * @param  string      $columnName
     * @param  string      $columnPropertyName
     * @param  mixed|null  $default
     *
     * @return mixed
     */
    public function getColumnProperty(string $columnName, string $columnPropertyName, mixed $default = null): mixed
    {
        if ($column = $this->getColumnByName($columnName)) {
            return Arr::get($column, $columnPropertyName);
        }

        return $default;
    }

    /**
     * @return string[]
     */
    public function getSelectionColumn(): array
    {
        return [
            'label'   => 'Select/Unselect',
            'view'    => 'data-table::livewire.js-dt.tables.columns.check',
            'css_all' => 'w-max text-start',
            //'css_header' => 'w-fit whitespace-nowrap text-nowrap',
            //'css_body' => 'text-center',
            'icon'    => 'circle-check',
        ];
    }

    /**
     * @return string[]
     */
    public function getDragAndDropColumn(): array
    {
        return [
            'label'   => 'Move/Reposition',
            'view'    => 'data-table::livewire.js-dt.tables.columns.drag-and-drop',
            'css_all' => 'w-max text-start',
            //'css_header' => 'w-fit whitespace-nowrap text-nowrap',
            'icon'    => 'columns-4',
        ];
    }

    /**
     * @return array
     */
    public function getActionsColumn(): array
    {
        return [
            'name'    => 'actions',
            'label'   => 'Actions',
            // 'visible' => fn() => $this->editable || $this->removable || $this->hasCommands,
            'visible' => function () {
                return $this->hasCommands && $this->rowCommands;
            },
            // just for clean understanding, but not needed because this method won't be called if false
            // 'visible' => true,
            'view'    => 'data-table::livewire.js-dt.tables.columns.actions',
            'css_all' => 'w-50 text-end',
            'icon'    => 'cog',
        ];
    }

    /**
     * @param  string|null  $collectionName
     *
     * @return bool
     */
    public function canDragAndDrop(?string $collectionName = null): bool
    {
        if (!$this->columnPosition) {
            return false;
        }
        if (!$collectionName) {
            return (bool) ($this->columnPosition);
        }

        return $this->sortColumnCount($collectionName) == 1 && $this->hasSort($collectionName, $this->columnPosition);
    }

    /**
     * Get all columns inclusive calculated ones like $this->selectable and $this->hasCommands.
     * Also fills with columns default values.
     * You can ensure the fields exists!
     *
     * @return array[]
     * @todo: use standard CacheService::remember()
     * @todo: change source for getFixCollection()
     */
    public function getAllColumns(): array
    {
        //
        if (!$this->allColumnsCached) {
            $this->allColumnsCached = $this->getColumns();

            if ($this->selectable) {
                $this->allColumnsCached = array_merge([$this->getSelectionColumn()], $this->allColumnsCached);
            }

            // drag and drop mover ...
            if ($this->canDragAndDrop()) {
                $this->allColumnsCached = array_merge([$this->getDragAndDropColumn()], $this->allColumnsCached);
            }

            if ($this->hasCommands) {
                if ($actionColumns = $this->getActionsColumn()) {
                    $this->allColumnsCached = array_merge($this->allColumnsCached, [$actionColumns]);
                }
            }

            // add defaults
            foreach ($this->allColumnsCached as &$column) {
                $column = array_merge($this->columnDefaults, $column);
            }
        }

        return $this->allColumnsCached;
    }

    /**
     * @param  array       $columnData
     * @param  string      $key
     * @param  mixed|null  $default
     *
     * @return mixed
     */
    public function getColumnParam(array &$columnData, string $key, mixed $default = null): mixed
    {
        if (isset($columnData[$key])) {
            if (app('system_base')->isCallableClosure($columnData[$key])) {
                return $columnData[$key]();
            }

            return $columnData[$key];
        }

        return $default;
    }

    /**
     * @param  string  $prefix
     * @param  string  $suffix
     *
     * @return string
     */
    protected function getCacheKey(string $prefix = '', string $suffix = ''): string
    {
        return ($prefix ? ($prefix.'_') : '').__CLASS__.'_'.$this->getEloquentModelName().($suffix ? ('_'.$suffix) : '');
    }

    /**
     * Get (cached) module model class like "App\Models\User"
     *
     * @return string
     */
    protected function getModuleModelClass(): string
    {
        return app(CacheService::class)->rememberUseConfig($this->getCacheKey(suffix: 'base_builder'), 'system-base.cache.object.signature.ttl', function () {
            if ((!$moduleClass = app('system_base')->findModuleClass($this->getEloquentModelName()))) {
                throw new Exception(sprintf("Model not found 1) %s 2) %s 3) %s", $this->getEloquentModelName(), $this->eloquentModelName, static::class));
            }

            return $moduleClass;
        });

    }

    /**
     * The base builder before all filter manipulations.
     * Usually used for all collections (default, selected, unselected), but can be overwritten.
     *
     * @param  string|null  $collectionName
     *
     * @return Builder|null
     * @throws Exception
     */
    public function getBaseBuilder(?string $collectionName = null): ?Builder
    {
        if ((!$moduleClass = $this->getModuleModelClass())) {
            throw new Exception(sprintf("Model not found %s - %s - %s", $this->getEloquentModelName(), $this->eloquentModelName, static::class));
        }

        /** @var Builder $builder */
        $builder = app($moduleClass)->with($this->objectRelations);

        // if column of userId was set and should filter by user ...
        if ($this->columnUserId && $this->filterByParentOwner) {
            $builder = $builder->where(function ($b) {
                $b->where($this->columnUserId, $this->getUserId())
                    ->orWhereNull($this->columnUserId);
            });
        }

        return $builder;
    }

    /**
     * @return string
     */
    public function getEloquentModelName(): string
    {
        if (!$this->eloquentModelName) {
            return app('system_base')->getSimpleClassName(static::class);
        } else {
            return app('system_base')->getSimpleClassName($this->eloquentModelName);
        }
    }

    /**
     * The builder inclusive filters
     *
     * @param  string  $collectionName
     *
     * @return Builder|null
     * @throws Exception
     */
    public function getBuilder(string $collectionName): ?Builder
    {
        if ($builder = $this->getBaseBuilder($collectionName)) {

            // find table name to avoid ambiguous columns
            $tableName = app('system_base')->getModelTable($this->getModuleModelClass());

            /**
             * collection name specific
             */
            switch ($collectionName) {
                case self::COLLECTION_NAME_SELECTED_ITEMS:
                    $builder->whereIn($tableName.'.id', $this->selectedItems);
                    break;

                case self::COLLECTION_NAME_UNSELECTED_ITEMS:
                    $builder->whereNotIn($tableName.'.id', $this->selectedItems);
                    break;
            }

            /**
             * Add external filters
             */
            $this->extendBuilderByFilters($builder, $collectionName);

            /**
             * Add search filter
             */ //$this->addSearchToCollectionOrBuilder($collectionName, $builder);

            // sorting / order by
            $this->addSortToCollectionOrBuilder($collectionName, $builder);

            //
            return $builder;
        }

        return null;
    }

    /**
     * Add sorting to builder or collection.
     *
     * @param  string                     $collectionName
     * @param  Builder|SupportCollection  $builder  (an eloquent Collection is a SupportCollection too)
     *
     * @return void
     */
    protected function addSortToCollectionOrBuilder(string $collectionName, Builder|SupportCollection &$builder): void
    {
        // sort if column is sortable only
        $sorts = [];
        foreach (data_get($this->filters, $collectionName.'.sort', []) as $sortColumn => $sortDirection) {
            $sortColumn = $this->replaceDot($sortColumn, true);
            if ($this->getColumnProperty($sortColumn, 'sortable', false)) {
                $sorts[$sortColumn] = $sortDirection;
            }
        }

        if (!$sorts) {
            return;
        }

        if ($builder instanceof Builder) {
            foreach ($sorts as $sortColumn => $sortDirection) {
                $builder->orderBy($sortColumn, $sortDirection);
            }

            return;
        }

        // One comparator for all sort columns (in order), so later columns only decide on equal values.
        // data_get() respects dot notations like 'pivot.position'.
        $builder = $builder->sort(function ($a, $b) use ($sorts) {
            foreach ($sorts as $sortColumn => $sortDirection) {
                $result = $this->compareSortValues(data_get($a, $sortColumn), data_get($b, $sortColumn));
                if ($result !== 0) {
                    return ($sortDirection === 'desc') ? -$result : $result;
                }
            }

            return 0;
        })
            ->values();
    }

    /**
     * Compare two cell values for in-memory sorting.
     * Numbers numerically, everything else as natural case-insensitive string (so "peer10" follows "peer9").
     * Empty values come first.
     *
     * @param  mixed  $a
     * @param  mixed  $b
     *
     * @return int
     */
    protected function compareSortValues(mixed $a, mixed $b): int
    {
        if (is_numeric($a) && is_numeric($b)) {
            return $a <=> $b;
        }
        if (is_bool($a) || is_bool($b)) {
            return (bool) $a <=> (bool) $b;
        }

        return strnatcasecmp($this->stringifyForSearch($a), $this->stringifyForSearch($b));
    }

    /**
     * Overwrite this to add custom filters
     * Also used for pagination
     *
     * @param  Builder  $builder
     * @param  string   $collectionName
     *
     * @return void
     */
    protected function extendBuilderByFilters(Builder $builder, string $collectionName): void
    {
        $this->processCurrentFilterElementsValues($builder, $collectionName);
    }

    /**
     * Used Builder (incl. filters) and add pagination.
     *
     * @param  string  $collectionName
     *
     * @return Builder|null
     * @throws Exception
     */
    public function getBuilderWithPagination(string $collectionName): ?Builder
    {
        $builder = $this->getBuilder($collectionName);

        $builder->forPage($this->getPaginationCurrentPage($collectionName),
            $this->getPaginationRowsPerPage($collectionName));

        return $builder;
    }

    /**
     * @param  string  $collectionName
     *
     * @return int
     */
    public function getPaginationCurrentPage(string $collectionName): int
    {
        return (int) data_get($this->filters, $collectionName.'.page', 1);
    }

    /**
     * livewire event
     *
     * @param  string  $collectionName
     * @param  int     $index
     *
     * @return void
     */
    #[On('set-pagination-current-page')]
    public function setPaginationCurrentPage(string $collectionName, int $index): void
    {
        $trys = 10;
        $max = $this->getPaginationMaxPage($collectionName);
        while ($trys > 0 && $index < $this->getPaginationMinPage($collectionName)) {
            $index += $max;
            $trys--;
        }
        while ($trys > 0 && $index > $max) {
            $index -= $max;
            $trys--;
        }
        if ($trys <= 0) {
            Log::error('$index issue. Trys overrun.', [
                __METHOD__,
                $trys,
                $index,
            ]);
            $index = 1;
        }
        //        $this->filters[$collectionName]['page'] = $index;
        //        $this->filters = data_set($this->filters, $collectionName . '.page', $index);
        data_set($this->filters, $collectionName.'.page', $index);
    }

    /**
     * @param  string  $collectionName
     *
     * @return int
     */
    public function getPaginationRowsPerPage(string $collectionName): int
    {
        return (int) (data_get($this->filters, $collectionName.'.rows_per_page', 10) ?? 10);
    }

    /**
     * @param  string  $collectionName
     *
     * @return int
     */
    public function getPaginationMinPage(string $collectionName): int
    {
        return 1;
    }

    /**
     * @param  string  $collectionName
     *
     * @return int
     */
    public function getPaginationMaxPage(string $collectionName): int
    {
        //dump([$collectionName, $this->getPaginationMaxRows($collectionName), $this->getPaginationRowsPerPage($collectionName)]);
        return (int) ceil($this->getPaginationMaxRows($collectionName) / $this->getPaginationRowsPerPage($collectionName));
    }

    /**
     * @param  string  $collectionName
     *
     * @return array
     */
    public function getPaginationGroupPages(string $collectionName): array
    {
        $groupSize = 9;
        $groupSizeHalf = floor($groupSize / 2);
        $minPage = $this->getPaginationMinPage($collectionName);
        $maxPage = $this->getPaginationMaxPage($collectionName);
        $currentPage = $this->getPaginationCurrentPage($collectionName);

        $showEndPage = $groupSize;
        if ($currentPage + $groupSizeHalf > $showEndPage) {
            $showEndPage = $currentPage + $groupSizeHalf;
        }

        if ($showEndPage > $maxPage) {
            $showEndPage = $maxPage;
        }

        $showStartPage = $showEndPage - $groupSize;
        if ($showStartPage < $this->getPaginationMinPage($collectionName)) {
            $showStartPage = $this->getPaginationMinPage($collectionName);
        }

        $pages = [];
        for ($i = $showStartPage; $i <= $showEndPage; $i++) {
            $pages[] = [
                'index'   => $i,
                'label'   => $i,
                'enabled' => ($i >= $showStartPage && $i <= $showEndPage),
                'active'  => ($i == $currentPage),
            ];
        }

        return $pages;
    }

    /**
     * @param  string  $collectionName
     *
     * @return int
     * @throws Exception
     */
    public function getPaginationMaxRows(string $collectionName): int
    {
        if (($fix = $this->getPreparedFixCollection($collectionName, false)) !== null) {
            return $fix->count();
        }

        $builder = $this->getBuilder($collectionName);

        return $builder->count();
    }

    /**
     * @param  string  $collectionName
     *
     * @return Collection|SupportCollection|array
     * @throws Exception
     */
    public function getCollection(string $collectionName): Collection|SupportCollection|array //Collection|\Illuminate\Support\Collection|array
    {
        // @todo: unfortunately collection can't be cached this way cause of the custom filters

        if (($fix = $this->getPreparedFixCollection($collectionName, true)) !== null) {
            $this->collections[$collectionName] = $fix;
        } else {
            $this->collections[$collectionName] = $this->getBuilderWithPagination($collectionName)
                ->get();
        }

        /**
         * If we have an id in parentData, then we will update every row objects relatedPivotModelId.
         * This is important to get the valid pivot data.
         * For Example see: \Modules\KlaraDeployment\app\Models\DeploymentTask::deployment()
         */
        if ($collectionName === self::COLLECTION_NAME_SELECTED_ITEMS) {
            if ($parentId = data_get($this->parentData, 'id')) {
                foreach ($this->collections[$collectionName] as $item) {
                    if (app('system_base')->hasInstanceClassOrTrait($item, TraitModelAddMeta::class)) {
                        $item->relatedPivotModelId = $parentId;
                    }
                }
            }
        }

        return $this->collections[$collectionName];
    }

    /**
     * Overwrite this for data tables without an eloquent model: return all rows (arrays or objects).
     * Return null to use the eloquent builder (default).
     * See $processFixCollection to get search, filters, sorting and pagination for these rows.
     *
     * @param  string  $collectionName
     *
     * @return SupportCollection|null
     */
    public function getFixCollection(string $collectionName): ?SupportCollection
    {
        return null;
    }

    /**
     * getFixCollection() memoized for the current request.
     *
     * @param  string  $collectionName
     *
     * @return SupportCollection|null
     */
    protected function getFixCollectionCached(string $collectionName): ?SupportCollection
    {
        if (!array_key_exists($collectionName, $this->fixCollectionCache)) {
            $this->fixCollectionCache[$collectionName] = $this->getFixCollection($collectionName);
        }

        return $this->fixCollectionCache[$collectionName];
    }

    /**
     * Call this after the source of getFixCollection() was changed in the current request (e.g. after deleting).
     *
     * @return void
     */
    public function forgetFixCollectionCache(): void
    {
        $this->fixCollectionCache = [];
    }

    /**
     * The fix collection as presented: unchanged if $processFixCollection is false,
     * otherwise like the builder: selected/unselected, filters (incl. search), sorting and (optional) pagination.
     *
     * @param  string  $collectionName
     * @param  bool    $paginate
     *
     * @return SupportCollection|null null if this table uses the eloquent builder
     */
    protected function getPreparedFixCollection(string $collectionName, bool $paginate): ?SupportCollection
    {
        $items = $this->getFixCollectionCached($collectionName);
        if ($items === null || !$this->processFixCollection) {
            return $items;
        }

        // collection name specific (like getBuilder())
        $selectedIds = array_map('strval', $this->selectedItems);
        switch ($collectionName) {
            case self::COLLECTION_NAME_SELECTED_ITEMS:
                $items = $items->filter(fn($item) => in_array((string) data_get($item, $this->columnNameId), $selectedIds, true));
                break;

            case self::COLLECTION_NAME_UNSELECTED_ITEMS:
                $items = $items->filter(fn($item) => !in_array((string) data_get($item, $this->columnNameId), $selectedIds, true));
                break;
        }

        $items = $this->processCurrentFilterElementsValuesForCollection($items, $collectionName);

        $this->addSortToCollectionOrBuilder($collectionName, $items);

        if ($paginate) {
            $items = $items->forPage($this->getPaginationCurrentPage($collectionName), $this->getPaginationRowsPerPage($collectionName));
        }

        return $items->values();
    }

    /**
     * @return Application|Factory|View
     */
    public function render(): Factory|View|Application
    {
        return view('data-table::livewire.js-dt.dt-auto');
    }

    /**
     * Shown while this component is lazy loaded (see 'lazy' in the element-dt-* form elements).
     *
     * @param  array  $params
     *
     * @return string
     */
    public function placeholder(array $params = []): string
    {
        return '<div class="min-h-24 flex items-center justify-center text-sm text-secondary-500"><span class="animate-pulse">'.e(__('Loading ...')).'</span></div>';
    }

    /**
     * @param  int|string  $id
     *
     * @return void
     */
    public function toggleSelectedItem(int|string $id): void
    {
        if (!in_array($id, $this->selectedItems)) {
            $this->selectedItems[] = $id;
        } else {
            if (($key = array_search($id, $this->selectedItems)) !== false) {
                unset($this->selectedItems[$key]);
            }
        }

        if (!$this->parentRelationMethodForThisBuilder) {
            // @todo: Error misconfiguration
            Log::error('Missing parentRelationMethodForThisBuilder', [__METHOD__]);

            return;
        }

        // @todo: this would refresh the whole parent form, which is not wanted, but needed to update relations in backend!
        $this->dispatch('update-relations', $this->parentRelationMethodForThisBuilder, $this->selectedItems);
    }

    /**
     * @param  int|string  $id
     *
     * @return bool
     */
    public function hasSelectedItem(int|string $id): bool
    {
        return in_array($id, $this->selectedItems);
    }

    /**
     * @return string
     */
    protected function getComponentFormName(): string
    {
        return $this->relatedLivewireForm;
    }

    /**
     * @return void
     */
    #[On('close-form')]
    public function closeForm(): void
    {
        if ($form = $this->getComponentFormName()) {
            $this->dispatch('close-form')
                ->to($form);
        }
    }

    /**
     * @param  mixed  $livewireId
     * @param  mixed  $itemId
     *
     * @return bool
     * @throws Exception
     */
    #[On('delete-item')]
    public function deleteItem(mixed $livewireId, mixed $itemId): bool
    {
        if (!$this->checkLivewireId($livewireId)) {
            return false;
        }

        // Use delete() for each item to respect delete events
        return !!$this->getBaseBuilder(self::COLLECTION_NAME_DEFAULT)
            ->whereKey($itemId)
            ->get()->each->delete();
    }

    /**
     * @param  mixed  $livewireId
     * @param  mixed  $itemId
     * @param  bool   $simulate
     *
     * @return bool
     */
    #[On('launch-item')]
    public function launchItem(mixed $livewireId, mixed $itemId, bool $simulate = false): bool
    {
        if (!$this->checkLivewireId($livewireId)) {
            return false;
        }

        return true;
    }

    /**
     * @param  mixed  $livewireId
     * @param  mixed  $itemId
     *
     * @return bool
     * @throws Exception
     */
    #[On('simulate-item')]
    public function simulateItem(mixed $livewireId, mixed $itemId): bool
    {
        return $this->launchItem($livewireId, $itemId, true);
    }

    /**
     * Sort does NOT work if column 'sortable' is set to false!
     * Sort same direction twice will remove this sort
     *
     * @param  string  $column
     * @param  string  $direction
     * @param  string  $collectionName
     *
     * @return void
     */
    #[On('set-sort')]
    public function setSort(string $column, string $direction, string $collectionName): void
    {
        $column = $this->replaceDot($column);
        if ($this->hasSort($collectionName, $column, $direction)) {
            // remove this sort
            Arr::forget($this->filters, $collectionName.'.sort.'.$column);
        } else {
            Arr::set($this->filters, $collectionName.'.sort.'.$column, $direction);
        }

        $this->updateFiltersSession();
    }

    /**
     * @param  string  $column
     * @param  string  $collectionName
     *
     * @return void
     */
    #[On('toggle-sort')]
    public function toggleSort(string $column, string $collectionName): void
    {
        $column = $this->replaceDot($column);
        $current = Arr::get($this->filters, $collectionName.'.sort.'.$column);
        switch ($current) {
            case 'asc':
                $current = 'desc';
                break;
            case 'desc':
                // keep 'desc' to reset by setSort()
                break;
            default:
                $current = 'asc';
                break;
        }
        $this->setSort($column, $current, $collectionName);
    }

    /**
     * @return void
     */
    #[On('reset-filters')]
    public function resetFilters(): void
    {
        $this->resetFiltersSession();
        $this->getFiltersSession();
    }

    /**
     * magic Hook/Event when updated $filters
     *
     * @param $value
     * @param $key
     *
     * @return void
     */
    public function updatedFilters($value, $key): void
    {
        foreach ($this->enabledCollectionNames as $collectionName => $collectionNameValue) {
            $prefix = $collectionName.'.';
            if (Str::startsWith($key, $prefix)) {
                $name = Str::after($key, $prefix);
                if (isset($this->filterElementConfig[$name])) {
                    $softReset = data_get($this->filterElementConfig, $name.'.soft_reset', false);
                    if ($softReset) {
                        // reset page to 1
                        data_set($this->filters, $collectionName.'.page', 1);
                        $this->updateFiltersSession();

                        return;
                    }
                }
            }
        }
    }

    /**
     *
     * @param  string  $key  like rows_per_page.default
     * @param  mixed   $value
     *
     * @return void
     */
    public function setDefaultFilter(string $key, mixed $value): void
    {
        data_set($this->filterElementConfig, $key, $value);
    }

    /**
     * Iterate all configured filter elements
     *
     * @param  callable  $filterElement
     *
     * @return void
     */
    public function iterateFilterElements(callable $filterElement): void
    {
        foreach ($this->getOrderedFilterElementConfig() as $elKey => $elValue) {
            $filterElement($elKey, $elValue);
        }
    }

    /**
     * Processing all configured filter elements and their option values (if any)
     * by adjusting the collection builder.
     *
     * @param  Builder  $builder
     * @param  string   $collectionName
     *
     * @return void
     */
    public function processCurrentFilterElementsValues(Builder $builder, string $collectionName): void
    {
        $this->runCurrentFilterCallbacks($collectionName, 'builder', function (callable $callback, string $filterElementKey, mixed $filterValue) use ($builder) {
            $callback($builder, $filterElementKey, $filterValue);
        });

        //Log::debug("Builder SQL: ".$builder->toSql());
    }

    /**
     * Same as processCurrentFilterElementsValues(), but for fix collections (see $processFixCollection).
     * Uses the 'collection_builder' callbacks of the filter elements; elements without one are ignored.
     *
     * @param  SupportCollection  $items
     * @param  string             $collectionName
     *
     * @return SupportCollection
     */
    public function processCurrentFilterElementsValuesForCollection(SupportCollection $items, string $collectionName): SupportCollection
    {
        $this->runCurrentFilterCallbacks($collectionName, 'collection_builder', function (callable $callback, string $filterElementKey, mixed $filterValue) use (&$items) {
            $result = $callback($items, $filterElementKey, $filterValue);
            if ($result instanceof SupportCollection) {
                $items = $result;
            }
        });

        return $items;
    }

    /**
     * Finds the callbacks of all filter elements for their current values and passes them to $invoke.
     *
     * Rules (unchanged from the former builder only version):
     * - element with options: only the option matching the current value counts.
     *   An option config array uses its own callback, a scalar option uses the callback of the element.
     * - element without options: the callback of the element.
     *
     * @param  string    $collectionName
     * @param  string    $callbackKey  'builder' or 'collection_builder'
     * @param  callable  $invoke       function (callable $callback, string $filterElementKey, mixed $filterValue)
     *
     * @return void
     */
    protected function runCurrentFilterCallbacks(string $collectionName, string $callbackKey, callable $invoke): void
    {
        // all filter elements ...
        $this->iterateFilterElements(function (string $filterElementKey, array $elementValueData) use ($collectionName, $callbackKey, $invoke) {
            // key like "unselected_items.rows_per_page"
            $filterKey = $collectionName.'.'.$filterElementKey;
            // current value like 20
            $filterValue = data_get($this->filters, $filterKey, '');

            // If has options ...
            if ($elementOptions = data_get($elementValueData, 'options')) {
                foreach ($elementOptions as $optionKey => $optionValue) {

                    // need type weak conditions like "!=" to compare 42 and "42"
                    if ($optionKey != $filterValue) {
                        continue;
                    }

                    // option value is a config array ...
                    if (is_array($optionValue)) {
                        // option config has its own callback ...
                        $optionCallback = data_get($optionValue, $callbackKey);
                        if ($optionCallback && app('system_base')->isCallableClosure($optionCallback)) {
                            $invoke($optionCallback, $filterElementKey, $filterValue);
                        }
                    } else {
                        // default callback for all options
                        if ($defaultCallback = data_get($elementValueData, $callbackKey)) {
                            $invoke($defaultCallback, $filterElementKey, $filterValue);
                        }
                    }

                }
            } else {
                // default callback for all values
                if ($defaultCallback = data_get($elementValueData, $callbackKey)) {
                    $invoke($defaultCallback, $filterElementKey, $filterValue);
                }
            }

        });
    }


    /**
     * Checks whether $columns is already sorted by this direction.
     *
     * @param  string       $collectionName
     * @param  string       $column
     * @param  string|null  $direction
     *
     * @return bool
     */
    protected function hasSort(string $collectionName, string $column, ?string $direction = null): bool
    {
        $column = $this->replaceDot($column);

        if (!$direction) {
            return Arr::has($this->filters, $collectionName.'.sort.'.$column);
        }

        return (Arr::get($this->filters, $collectionName.'.sort.'.$column) === $direction);
    }

    /**
     * @param  string  $collectionName
     *
     * @return int
     */
    protected function sortColumnCount(string $collectionName): int
    {
        return count(Arr::get($this->filters, $collectionName.'.sort', []));
    }

    /**
     * Replace dot with a rare item.
     * This is important to save dot-notated columns in arrays like 'pivot.position' in array 'x.sort...'
     * so we get elements like ['x']['sort']['pivot.position'].
     *
     * @param  string  $subject
     * @param  bool    $replaceBack
     * @param  string  $replaceTo
     *
     * @return string
     */
    protected function replaceDot(string $subject, bool $replaceBack = false, string $replaceTo = '_[[<|#|>]]_'): string
    {
        if ($replaceBack) {
            return str_replace($replaceTo, '.', $subject);
        }

        return str_replace('.', $replaceTo, $subject);
    }

    /**
     * Sort does NOT work if column 'sortable' is set to false!
     *
     * @param  string  $column
     * @param  string  $direction
     *
     * @return void
     */
    public function setSortAllCollections(string $column, string $direction): void
    {
        foreach ($this->enabledCollectionNames as $name => $enabled) {
            $this->setSort($column, $direction, $name);
        }
    }

    /**
     * @param $item
     *
     * @return bool
     */
    public function canItemRemoved($item): bool
    {
        return true;
    }

    /**
     * @param $item
     *
     * @return bool
     */
    protected function isItemValid($item): bool
    {
        return true;
    }

    /**
     * @param $item
     *
     * @return bool
     */
    protected function isItemWarn($item): bool
    {
        return false;
    }

    /**
     * Used to sort rows by drag and drop.
     *
     * @param            $collectionName
     * @param            $movedId
     * @param  int|null  $prevId
     * @param  int|null  $nextId
     *
     * @return void
     * @throws Exception
     */
    public function updateOrder($collectionName, $movedId, ?int $prevId = null, ?int $nextId = null): void
    {
        if ((!$this->columnPosition) || $this->sortColumnCount($collectionName) != 1 || !$this->hasSort($collectionName, $this->columnPosition)) {
            $this->addErrorMessage(__('Unable to reposition', ['name' => $this->columnPosition]));

            return;
        }

        // Rebase positions before we do the task.
        // In most cases this is not needed, but if a lot of new items were added with like default 1000,
        // they cannot move into each other.
        $this->rebasePositions();

        $prevPos = $prevId ? $this->getBaseBuilder()
            ->find($prevId)->position : 0;
        $nextPos = $nextId ? $this->getBaseBuilder()
            ->find($nextId)->position : null;

        // calc new position
        if ($nextPos === null) {
            // to bottom
            $newPos = $prevPos + 1000;
        } elseif ($prevPos === 0) {
            // on top
            $newPos = $nextPos / 2;
        } else {
            // middle
            $newPos = ($prevPos + $nextPos) / 2;
        }

        //$this->addInfoMessage($this->getBuilder($collectionName)->toRawSql());
        $this->getBaseBuilder()
            ->where('id', $movedId)
            ->update([$this->columnPosition => $newPos]);
    }

    /**
     * Rewrite all positions to generous values
     *
     * @return void
     * @todo: call it sometimes to flat the position values
     */
    public function rebasePositions(): void
    {
        if ((!($userId = $this->getUserId())) || !$this->columnUserId) {
            $this->addErrorMessage(__('Unable to reposition. No user detected or no user column defined.'));

            return;
        }

        try {
            DB::transaction(function () use ($userId) {
                $i = 1000;
                foreach (
                    $this->getBaseBuilder()
                        ->where('user_id', $userId)
                        ->orderBy($this->columnPosition)
                        ->get() as $model
                ) {
                    $model->update([$this->columnPosition => $i]);
                    $i += 1000;
                }
            });
        } catch (\Throwable $exception) {
            $this->addErrorMessage($exception->getMessage());
        }
    }
}
