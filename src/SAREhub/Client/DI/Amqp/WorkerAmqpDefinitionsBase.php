<?php


namespace SAREhub\Client\DI\Amqp;


use SAREhub\Client\Amqp\AmqpChannelWrapper;
use SAREhub\Client\Amqp\AmqpConnectionService;
use SAREhub\Client\Amqp\Task\RegisterAmqpConsumerTask;
use SAREhub\Client\DI\Worker\WorkerDefinitions;
use function DI\add;
use function DI\create;
use function DI\get;

class WorkerAmqpDefinitionsBase
{
    public static function get(): array
    {
        return [
            WorkerDefinitions::ENTRY_INIT_TASKS => add(static::initTasksDef()),
            WorkerDefinitions::ENTRY_WORKER_SERVICES => add([
                get(AmqpConnectionService::class)
            ])
        ];
    }

    private static function initTasksDef()
    {
        $channelDef = get(AmqpChannelWrapper::class);
        $consumerTasks = [];
        foreach (static::getConsumersToRegister() as $consumer) {
            $consumerTasks[] = create(RegisterAmqpConsumerTask::class)->constructor($channelDef, $consumer);
        }
        return $consumerTasks;
    }

    protected static function getConsumersToRegister(): array
    {
        return [];
    }
}
