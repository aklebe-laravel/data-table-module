<?php

namespace Modules\DataTable\app\Listeners;

use Illuminate\Support\Facades\Log;
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
        Log::debug(__METHOD__, [$event->moduleName, $event->classes]);

        $makeDataTableService = app(MakeDataTablesService::class);
        foreach ($event->classes as $class) {
            $makeDataTableService->makeDataTable($event->moduleName, $class);
        }
    }
}
