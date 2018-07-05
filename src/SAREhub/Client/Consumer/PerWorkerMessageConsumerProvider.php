<?php


namespace SAREhub\Client\Consumer;


use SAREhub\Client\Amqp\AmqpConsumer;
use SAREhub\Client\Amqp\AmqpConsumerOptions;
use SAREhub\Client\Processor\Processor;
use SAREhub\Commons\Misc\InvokableProvider;

class PerWorkerMessageConsumerProvider extends InvokableProvider
{
    const QUEUE_NAME_KEY = "queueName";
    const TAG_PREFIX_KEY = "tagPrefix";
    const WORKER_ID_KEY = "workerId";

    const TAG_PATTERN = "%s_%d";

    const DEFAULT_TAG_PREFIX = "consumer_tag";

    /**
     * @var string
     */
    private $queueName;

    /**
     * @var string
     */
    private $tagPrefix;

    /**
     * @var int
     */
    private $workerId;

    /**
     * @var Processor
     */
    private $processor;


    /**
     * PerWorkerMessageConsumerProvider constructor.
     * @param array $options
     * @param Processor $processor
     * @throws \Exception
     */
    public function __construct(array $options = [], Processor $processor)
    {
        $this->setQueueNameFromOptions($options);
        $this->setWorkerIdFromOptions($options);
        $this->setTagPrefixFromOptions($options);
        $this->processor = $processor;
    }

    public function get()
    {
        return new AmqpConsumer($this->createOptions(), $this->processor);
    }

    private function createOptions(): AmqpConsumerOptions
    {
        return AmqpConsumerOptions::newInstance()
            ->setQueueName($this->queueName)
            ->setTag(sprintf(self::TAG_PATTERN, $this->tagPrefix, $this->workerId));
    }

    /**
     * @param array $options
     * @throws \Exception
     */
    private function setQueueNameFromOptions(array $options): void
    {
        if (!isset($options[self::QUEUE_NAME_KEY])) {
            throw new \Exception("queueName not set in provider constructor options.");
        }
        $this->queueName = $options[self::QUEUE_NAME_KEY];
    }

    /**
     * @param array $options
     * @throws \Exception
     */
    private function setWorkerIdFromOptions(array $options): void
    {
        if (!isset($options[self::WORKER_ID_KEY])) {
            throw new \Exception("workerId not set in provider constructor options.");
        }
        $this->workerId = $options[self::WORKER_ID_KEY];
    }

    /**
     * @param array $options
     * @throws \Exception
     */
    private function setTagPrefixFromOptions(array $options): void
    {
        if (!isset($options[self::TAG_PREFIX_KEY])) {
            $options[self::TAG_PREFIX_KEY] = self::DEFAULT_TAG_PREFIX;
        }
        $this->tagPrefix = $options[self::TAG_PREFIX_KEY];
    }

    public function getQueueName(): string
    {
        return $this->queueName;
    }

    public function getTagPrefix(): string
    {
        return $this->tagPrefix;
    }

    public function getWorkerId(): int
    {
        return $this->workerId;
    }

}