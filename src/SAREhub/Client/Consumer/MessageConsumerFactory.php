<?php


namespace SAREhub\Client\Consumer;


use function DI\create;
use DI\Definition\Helper\CreateDefinitionHelper;
use SAREhub\Client\Amqp\AmqpConsumer;
use SAREhub\Client\Amqp\AmqpConsumerOptions;

class MessageConsumerFactory
{
    public static function createExclusive($queueName, $processor): CreateDefinitionHelper
    {
        return create(AmqpConsumer::class)
            ->constructor(
                (new AmqpConsumerOptions())->setQueueName($queueName),
                $processor
            );
    }

    public static function create($queueName, $workerId, $tagPattern, $processor): CreateDefinitionHelper
    {
        $options = (new AmqpConsumerOptions())->setQueueName($queueName)->setTag(sprintf($tagPattern, $workerId));

        return create(AmqpConsumer::class)->constructor($options, $processor);
    }
}