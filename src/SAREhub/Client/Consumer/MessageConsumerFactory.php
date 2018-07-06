<?php


namespace SAREhub\Client\Consumer;


use DI\Definition\Helper\FactoryDefinitionHelper;
use function DI\factory;

class MessageConsumerFactory
{
    public static function createExclusive(string $queueName, $processor): FactoryDefinitionHelper
    {
        return factory(MessageConsumerProvider::class)
            ->parameter("consumerOptions", (new AmqpConsumerOptions())->setQueueName($queueName))
            ->parameter("processor", $processor);
    }

    public static function create($queueName, $workerId, $tagPattern, $processor): FactoryDefinitionHelper
    {
        $options = (new AmqpConsumerOptions())->setQueueName($queueName)->setTag(sprintf($tagPattern, $workerId));

        return factory(MessageConsumerProvider::class)
            ->parameter("consumerOptions", $options)
            ->parameter("processor", $processor);
    }
}