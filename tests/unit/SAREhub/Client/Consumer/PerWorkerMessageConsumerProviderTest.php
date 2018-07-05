<?php

namespace SAREhub\Client\Consumer;


use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SAREhub\Client\Processor\Processor;

class PerWorkerMessageConsumerProviderTest extends TestCase
{
    /**
     * @var MockInterface | Processor
     */
    private $processor;

    public function setUp()
    {
        $this->processor = \Mockery::mock(Processor::class);
    }

    /**
     * @throws \Exception
     */
    public function testConstructorWhenAllOptionsSet()
    {
        $provider = new PerWorkerMessageConsumerProvider([
            PerWorkerMessageConsumerProvider::QUEUE_NAME_KEY => "test_queue_name",
            PerWorkerMessageConsumerProvider::TAG_PREFIX_KEY => "test_tag_prefix",
            PerWorkerMessageConsumerProvider::WORKER_ID_KEY => 1
        ], $this->processor);

        $this->assertEquals("test_queue_name", $provider->getQueueName());
        $this->assertEquals("test_tag_prefix", $provider->getTagPrefix());
        $this->assertEquals(1, $provider->getWorkerId());
    }

    /**
     * @throws \Exception
     */
    public function testConstructorWhenQueueNameNotSet()
    {
        $this->expectException(\Exception::class);

        new PerWorkerMessageConsumerProvider([
            PerWorkerMessageConsumerProvider::TAG_PREFIX_KEY => "test_tag_prefix",
            PerWorkerMessageConsumerProvider::WORKER_ID_KEY => 1
        ], $this->processor);

    }

    /**
     * @throws \Exception
     */
    public function testConstructorWhenTagPrefixNotSet()
    {
        $provider = new PerWorkerMessageConsumerProvider([
            PerWorkerMessageConsumerProvider::QUEUE_NAME_KEY => "test_queue_name",
            PerWorkerMessageConsumerProvider::WORKER_ID_KEY => 1
        ], $this->processor);

        $this->assertEquals("test_queue_name", $provider->getQueueName());
        $this->assertEquals(PerWorkerMessageConsumerProvider::DEFAULT_TAG_PREFIX, $provider->getTagPrefix());
        $this->assertEquals(1, $provider->getWorkerId());
    }

    /**
     * @throws \Exception
     */
    public function testConstructorWhenWorkerIdNotSet()
    {
        $this->expectException(\Exception::class);

        new PerWorkerMessageConsumerProvider([
            PerWorkerMessageConsumerProvider::QUEUE_NAME_KEY => "test_queue_name",
            PerWorkerMessageConsumerProvider::TAG_PREFIX_KEY => "test_tag_prefix",
        ], $this->processor);
    }
}
