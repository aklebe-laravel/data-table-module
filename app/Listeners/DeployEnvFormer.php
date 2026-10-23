<?php
declare(strict_types = 1);

namespace Modules\DataTable\app\Listeners;

use Modules\DataTable\app\Services\MakeDataTablesService;

class DeployEnvFormer
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(\Modules\DeployEnv\app\Events\DeployEnvFormer $event): void
    {
        $makeDataTableService = app(MakeDataTablesService::class);
        foreach ($event->classes as $class) {
            $makeDataTableService->makeDataTable($event->moduleName, $class);
        }
    }
}
