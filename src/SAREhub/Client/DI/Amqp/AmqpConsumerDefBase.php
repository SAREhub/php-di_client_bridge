<?php


namespace SAREhub\Client\DI\Amqp;


use function DI\get;

abstract class AmqpConsumerDefBase
{
    public static function getConsumerDef()
    {
        return AmqpDefinitionHelper::consumer(
            static::getQueueName(),
            static::getConsumerTag(),
            get(static::getProcessorEntry())
        );
    }

    public static abstract function getProcessorEntry(): string;

    public static abstract function getQueueName(): string;

    public static abstract function getConsumerTag(): string;

    public static abstract function getEnvSchemaDecorator(): callable;
}

