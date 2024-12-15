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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Modules\Acl\app\Models\AclResource;
use Modules\Acl\app\Services\UserService;
use Modules\SystemBase\app\Http\Livewire\BaseComponent;
use Modules\SystemBase\app\Models\Base\TraitModelAddMeta;
use Modules\SystemBase\app\Services\ModelService;

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
    protected array $objectRelations = [];

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
    public bool $useCollectionUserFilter = true;

    /**
     * Enable/disable delete actions.
     * Usually changed as livewire parameter.
     *
     * @var bool
     */
    public bool $removable = true;

    /**
     * Enables checkbox and bulk actions.
     *
     * @var bool
     */
    public bool $selectable = true;

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
    protected array $rowCommands = [
        'duplicate' => 'data-table::livewire.js-dt.tables.columns.buttons.duplicate',
        'edit'      => 'data-table::livewire.js-dt.tables.columns.buttons.edit',
        'delete'    => 'data-table::livewire.js-dt.tables.columns.buttons.delete',
    ];

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
     * @var array
     */
    protected array $columnDefaults = [
        'name'        => '',
        'value'       => '',
        'label'       => '???',
        'searchable'  => false,
        'sortable'    => false,
        'visible'     => true,
        // css column content
        'css_body'    => '',
        // css header
        'css_header'  => '',
        // css footer
        'css_footer'  => '',
        // css header, footer and content
        'css_all'     => '',
        // 'number', 'timestamp', ...
        'format'      => '',
        // custom view path for content
        'view'        => '',
        // without view only. If true, use __(...) for values
        'translation' => false,
        // future used ...
        'options'     => [],
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
        $this->initFilters();

        $this->getFiltersSession();
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
            'default'    => 10,
            'position'   => 1000,
            'soft_reset' => true,
            'css_group'  => 'col-12 col-md-3 text-start',
            'css_item'   => '',
            'options'    => [
                10  => 10,
                20  => 20,
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
                'label'      => 'Search ...',
                'default'    => '',
                'position'   => 2000,
                'soft_reset' => true,
                'css_group'  => 'col-12 col-md text-end',
                'css_item'   => '',
                'builder'    => function (Builder $builder, string $filterElementKey, string $filterValue) {
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
                'view'       => 'data-table::livewire.js-dt.filters.default-elements.text',
            ]);
        $this->addFilterElement('actions',
            [
                'label'     => 'Actions',
                'position'  => 9000,
                'css_group' => 'col-12 col-md-1 text-end',
                'css_item'  => '',
                'view'      => 'data-table::livewire.js-dt.filters.settings.default',
            ]);
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
        if (($parentModelId = data_get($this->parentData, 'id', ''))
            && ($parentModelName = data_get($this->parentData,
                'model_class', ''))
        ) {
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
            return Auth::user();
        } else {
            // @todo: cache or component public property
            return app(User::class)
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

        return $userService->hasUserResource(Auth::user(), AclResource::RES_MANAGE_USERS);
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
            'label'    => '',
            'view'     => 'data-table::livewire.js-dt.tables.columns.check',
            'css_all'  => 'text-center w-5',
            'css_body' => 'text-center',
            'icon'     => 'clipboard-check',
        ];
    }

    /**
     * @return array
     */
    public function getActionsColumn(): array
    {
        return [
            'label'   => 'Actions',
            // 'visible' => fn() => $this->editable || $this->removable || $this->hasCommands,
            'visible' => function () {
                return $this->hasCommands && $this->rowCommands;
            },
            // just for clean understanding, but not needed because this method won't be called if false
            // 'visible' => true,
            'view'    => 'data-table::livewire.js-dt.tables.columns.actions',
            'css_all' => 'text-end w-15',
            'icon'    => 'gear',
        ];
    }

    /**
     * Get all columns inclusive calculated ones like $this->selectable and $this->hasCommands.
     * Also fills with columns default values.
     * You can ensure the fields exists!
     *
     * @return array[]
     * @todo: use standard Cache::remember()
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
        //        return Cache::driver('array')->...
        $ttlDefault = config('system-base.cache.default_ttl', 1);
        $ttl = config('system-base.cache.object.signature.ttl', $ttlDefault);

        return Cache::remember($this->getCacheKey(suffix: 'base_builder'), $ttl, function () {
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
     * @param  string  $collectionName
     *
     * @return Builder|null
     * @throws Exception
     */
    public function getBaseBuilder(string $collectionName): ?Builder
    {
        if ((!$moduleClass = $this->getModuleModelClass())) {
            throw new Exception(sprintf("Model not found %s - %s - %s", $this->getEloquentModelName(), $this->eloquentModelName, static::class));
        }

        /** @var Builder $builder */
        $builder = app($moduleClass)->with($this->objectRelations);

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
             */
            //$this->addSearchToCollectionOrBuilder($collectionName, $builder);

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
     * @param  string              $collectionName
     * @param  Builder|Collection  $builder
     *
     * @return void
     */
    protected function addSortToCollectionOrBuilder(string $collectionName, Builder|Collection &$builder): void
    {
        foreach (data_get($this->filters, $collectionName.'.sort', []) as $sortColumn => $sortDirection) {
            $sortColumn = $this->replaceDot($sortColumn, true);
            // sort if column is sortable only
            if ($this->getColumnProperty($sortColumn, 'sortable', false)) {
                if ($builder instanceof Builder) {
                    $builder->orderBy($sortColumn, $sortDirection);
                } else {
                    // $builder->sortBy($sortColumn, ($sortDirection === 'desc') ? SORT_DESC : SORT_ASC);
                    // collection is unable to respect dot nations like 'pivot.position', so we do it by ourselves
                    $builder = $builder->sort(function ($a, $b) use ($sortColumn, $sortDirection) {
                        $a1 = data_get($a, $sortColumn);
                        $b1 = data_get($b, $sortColumn);

                        return ($sortDirection === 'desc') ? ($a1 < $b1) : ($a1 > $b1);
                    });
                }
            }
        }
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
        return (int) data_get($this->filters, $collectionName.'.rows_per_page', 6);
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
        if (($fix = $this->getFixCollection($collectionName)) !== null) {
            return $fix->count();
        }

        $builder = $this->getBuilder($collectionName);

        return $builder->count();
    }

    /**
     * @param  string  $collectionName
     *
     * @return Collection|\Illuminate\Support\Collection|array
     * @throws Exception
     */
    public function getCollection(string $collectionName): Collection|\Illuminate\Support\Collection|array //Collection|\Illuminate\Support\Collection|array
    {
        // @todo: unfortunately collection can't be cached this way cause of the custom filters

        if (($fix = $this->getFixCollection($collectionName)) !== null) {
            $this->collections[$collectionName] = $fix;
        } else {
            $this->collections[$collectionName] = $this->getBuilderWithPagination($collectionName)->get();
        }

        /**
         * If we have an id in parentData, then we will update every row objects relatedPivotModelId.
         * This important to get the valid pivot data.
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
     * @param  string  $collectionName
     *
     * @return \Illuminate\Support\Collection|null
     */
    public function getFixCollection(string $collectionName): ?\Illuminate\Support\Collection
    {
        return null;
    }

    /**
     * @return Application|Factory|View
     */
    public function render(): Factory|View|Application
    {
        return view('data-table::livewire.js-dt.dt-auto');
    }

    /**
     * @param  int  $id
     *
     * @return void
     */
    public function toggleSelectedItem(int $id): void
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
     * @param  int  $id
     *
     * @return bool
     */
    public function hasSelectedItem(int $id): bool
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

        return !!$this->getBaseBuilder(self::COLLECTION_NAME_DEFAULT)
                      ->whereKey($itemId)
                      ->delete();
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
     * Sort does NOT work if column 'searchable' is set to false!
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
        if ($this->hasSort($column, $direction, $collectionName)) {
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
        // all filter elements ...
        $this->IterateFilterElements(function (string $filterElementKey, array $elementValueData) use ($builder, $collectionName) {

            // key like "unselected_items.rows_per_page"
            $filterKey = $collectionName.'.'.$filterElementKey;
            // current value like 20
            $filterValue = data_get($this->filters, $filterKey, '');

            // If has options ...
            if ($elementOptions = data_get($elementValueData, 'options')) {
                foreach ($elementOptions as $optionKey => $optionValue) {

                    // The needed option ...
                    if ($optionKey !== $filterValue) {
                        continue;
                    }

                    // option value is a config array ...
                    if (is_array($optionValue)) {

                        if ($optionBuilderCallback = data_get($optionValue, 'builder')) {

                            // option config has a builder callback ...
                            if (app('system_base')->isCallableClosure($optionBuilderCallback)) {

                                $optionBuilderCallback($builder, $filterElementKey, $filterValue);
                                //Log::debug("config option (own builder) \"$filterElementKey\", value \"$optionKey\":\"$filterValue\"");

                            }
                        }
                    } else {
                        // default builder callback for all options
                        if ($defaultCallback = data_get($elementValueData, 'builder')) {
                            //Log::debug("scalar option \"$filterElementKey\", value \"$optionKey\":\"$filterValue\"");
                            $defaultCallback($builder, $filterElementKey, $filterValue);
                        }
                    }

                }
            } else {
                // default builder callback for all values
                if ($defaultCallback = data_get($elementValueData, 'builder')) {
                    //Log::debug("scalar element \"$filterElementKey\", value \"$filterValue\"");
                    $defaultCallback($builder, $filterElementKey, $filterValue);
                }
            }

        });

        //Log::debug("Builder SQL: ".$builder->toSql());
    }


    /**
     * Checks whether $columns is already sorted by this direction.
     *
     * @param  string  $column
     * @param  string  $direction
     * @param  string  $collectionName
     *
     * @return bool
     */
    protected function hasSort(string $column, string $direction, string $collectionName): bool
    {
        $column = $this->replaceDot($column);

        return (Arr::get($this->filters, $collectionName.'.sort.'.$column) === $direction);
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
     * Sort does NOT work if column 'searchable' is set to false!
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
     * @param $column
     *
     * @return string
     */
    public function renderIcon($column): string
    {
        if ($icon = data_get($column, 'icon')) {
            return '<span class="bi bi-'.$icon.'"></span>';
        }

        return '';
    }

}
