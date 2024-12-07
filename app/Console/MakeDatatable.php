<?php

namespace Modules\DataTable\app\Console;

use Exception;
use Illuminate\Console\Command;
use Modules\DataTable\app\Services\MakeDataTablesService;
use Modules\SystemBase\app\Services\ModuleService;
use Symfony\Component\Console\Command\Command as CommandResult;

class MakeDatatable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-table:make {table_name} {module_name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a datatable';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle(): int
    {
        if (!($dataTableName = $this->argument('table_name'))) {
            $this->error("Missing DataTable name!");
            return CommandResult::FAILURE;
        }
        $moduleName = $this->argument('module_name');

        $moduleName = ModuleService::getStudlyName($moduleName);
        $dataTableName = ModuleService::getStudlyName($dataTableName);

        $this->info(sprintf("DataTable: %s::%s", $moduleName, $dataTableName));

        $makeDataTableService = app(MakeDataTablesService::class);
        return $makeDataTableService->makeDataTable($moduleName, $dataTableName) ? CommandResult::SUCCESS : CommandResult::FAILURE;
    }

}
