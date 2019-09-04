<?php


namespace SAREhub\Client\DI\Amqp;


use function DI\create;
use SAREhub\Client\Amqp\AmqpConsumer;
use SAREhub\Client\Amqp\AmqpConsumerOptions;
use function DI\value;

class AmqpDefinitionHelper
{
    public static function consumer(string $queueName, string $tag, $processor)
    {
        $options = AmqpConsumerOptions::newInstance()
            ->setQueueName($queueName)
            ->setTag($tag);
        return create(AmqpConsumer::class)->constructor(value($options), $processor);
    }

    public static function exclusiveConsumer(string $queueName, $processor)
    {
        $options = AmqpConsumerOptions::newInstance()
            ->setQueueName($queueName)
            ->setExclusive(true);
        return create(AmqpConsumer::class)->constructor(value($options), $processor);
    }
}
