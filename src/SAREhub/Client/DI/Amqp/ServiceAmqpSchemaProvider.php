<?php


namespace SAREhub\Client\DI\Amqp;

use SAREhub\Client\Amqp\Schema\AmqpEnvironmentSchema;
use SAREhub\Commons\Misc\InvokableProvider;

class ServiceAmqpSchemaProvider extends InvokableProvider
{

    /**
     * @var array
     */
    private $decorators;

    public function __construct(array $decorators)
    {
        $this->decorators = $decorators;
    }

    public function get()
    {
        $schema = AmqpEnvironmentSchema::newInstance();
        foreach ($this->decorators as $decorator) {
            $decorator($schema);
        }
        return $schema;
    }
}
