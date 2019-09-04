<?php

namespace SAREhub\Client\DI\Worker;

use DI\Definition\Helper\DefinitionHelper;
use SAREhub\Commons\Logger\BasicLoggingDefinitions;
use SAREhub\Commons\Misc\EnvironmentHelper;
use SAREhub\Commons\Service\ServiceManager;
use SAREhub\Commons\Task\Sequence;
use SAREhub\DockerUtil\Worker\StandardWorker;
use SAREhub\DockerUtil\Worker\Worker;
use function DI\add;
use function DI\create;
use function DI\get;

class WorkerDefinitions
{
    const WORKER_LOGGER_NAME = "Worker";

    const ENTRY_WORKER_SERVICES = "Worker.services";
    const ENTRY_INIT_TASKS = "Worker.initTasks";

    const ENTRY_WORKERID = "Service.workerId";
    const ENV_WORKER_ID = "WORKERID";

    public static function get(): array
    {
        return [
            self::ENTRY_WORKERID => self::getWorkerIdFromEnv(),
            Worker::class => self::workerDefinition(),
            self::ENTRY_WORKER_SERVICES => add([])
        ];
    }

    protected static function workerDefinition(): DefinitionHelper
    {
        $initTask = self::workerInitTaskDefinition();
        $serviceManager = self::workerServiceManagerDefinition();
        $worker = create(StandardWorker::class)->constructor($initTask, $serviceManager);
        return BasicLoggingDefinitions::inject($worker, self::WORKER_LOGGER_NAME);
    }

    protected static function workerInitTaskDefinition(): DefinitionHelper
    {
        return create(Sequence::class)->constructor(get(self::ENTRY_INIT_TASKS));
    }

    protected static function workerServiceManagerDefinition(): DefinitionHelper
    {
        return create(ServiceManager::class)->constructor(get(self::ENTRY_WORKER_SERVICES));
    }

    public static function getWorkerIdFromEnv(): string
    {
        return EnvironmentHelper::getRequiredVar(self::ENV_WORKER_ID);
    }
}
