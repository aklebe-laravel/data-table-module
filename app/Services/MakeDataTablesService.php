<?php

namespace Modules\DataTable\app\Services;

use Exception;
use Modules\DeployEnv\app\Services\MakeModuleService;
use Modules\SystemBase\app\Services\Base\BaseService;
use Modules\SystemBase\app\Services\ModuleService;

class MakeDataTablesService extends BaseService
{
    /**
     * @param  string  $moduleName
     * @param  string  $dataTableName
     * @return bool
     * @throws Exception
     */
    public function makeDataTable(string $moduleName, string $dataTableName): bool
    {
        // get stubs path
        /** @var MakeModuleService $makeModuleService */
        $makeModuleService = app(MakeModuleService::class);
        $pathRootTemplate = ModuleService::getPath('module-stubs/DataTableTemplate', 'DataTable', 'resources');

        // adjust the parser placeholders
        $makeModuleService->additionalParserPlaceHolders['class_name'] = [
            'parameters' => [],
            'callback'   => function (array $placeholderParameters, array $parameters, array $recursiveData) use (
                $dataTableName
            ) {
                return $dataTableName;
            },
        ];

        // generate the files
        if ($makeModuleService->generateModuleFiles($moduleName, true, $pathRootTemplate)) {
            // $this->info("DataTable files successful generated!");
        } else {
            $this->error("DataTable files failed!");
            return false;
        }

        return true;
    }
}