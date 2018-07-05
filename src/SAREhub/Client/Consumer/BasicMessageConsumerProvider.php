<?php


namespace SAREhub\Client\Consumer;


use SAREhub\Client\Amqp\AmqpConsumer;
use SAREhub\Client\Amqp\AmqpConsumerOptions;
use SAREhub\Client\Processor\Processor;
use SAREhub\Commons\Misc\InvokableProvider;

class BasicMessageConsumerProvider extends InvokableProvider
{
    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var string
     */
    private $queueName;

    public function __construct(string $queueName, Processor $processor)
    {
        $this->queueName = $queueName;
        $this->processor = $processor;
    }

    public function get()
    {
        return new AmqpConsumer($this->createOptions(), $this->processor);
    }

    private function createOptions(): AmqpConsumerOptions
    {
        return AmqpConsumerOptions::newInstance()
            ->setExclusive(true)
            ->setQueueName($this->queueName);
    }
}