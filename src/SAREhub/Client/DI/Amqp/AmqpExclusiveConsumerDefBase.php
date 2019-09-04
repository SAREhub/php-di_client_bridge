<?php


namespace SAREhub\Client\DI\Amqp;


use function DI\get;

abstract class AmqpExclusiveConsumerDefBase
{
    public static function getConsumerDef()
    {
        return AmqpDefinitionHelper::exclusiveConsumer(static::getQueueName(), get(static::getProcessorEntry()));
    }

    public static abstract function getProcessorEntry(): string;

    public static abstract function getQueueName(): string;

    public static abstract function getEnvSchemaDecorator(): callable;
}

