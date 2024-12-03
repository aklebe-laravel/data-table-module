<?php

namespace Modules\DataTable\app\Providers;

use Modules\DataTable\app\Console\MakeDatatable;
use Modules\SystemBase\app\Providers\Base\ModuleBaseServiceProvider;

class DataTableServiceProvider extends ModuleBaseServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected string $moduleName = 'DataTable';

    /**
     * @var string $moduleNameLower
     */
    protected string $moduleNameLower = 'data-table';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        $this->commands([
            MakeDatatable::class,
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        parent::register();

        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
    }

}
