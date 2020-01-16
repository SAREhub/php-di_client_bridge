<?php


namespace SAREhub\Client\DI\Amqp;

use SAREhub\Client\Amqp\AmqpChannelWrapper;
use SAREhub\Client\Amqp\AmqpChannelWrapperProvider;
use SAREhub\Client\Amqp\AmqpConnectionOptions;
use SAREhub\Client\Amqp\AmqpConnectionService;
use SAREhub\Client\Amqp\AmqpMessageHeaders;
use SAREhub\Client\Amqp\AmqpProducer;
use SAREhub\Client\Amqp\EnvAmqpConnectionOptionsProvider;
use SAREhub\Client\Amqp\Schema\AmqpEnvironmentManager;
use SAREhub\Client\Amqp\Schema\AmqpEnvironmentSchemaCreator;
use SAREhub\Client\DataFormat\JsonDataFormat;
use SAREhub\Client\DI\Processor\ProcessorDefinitionHelper;
use SAREhub\Client\Event\Event;
use SAREhub\Client\Event\RawEventDataFormat;
use SAREhub\Client\Message\Exchange;
use SAREhub\Commons\Logger\BasicLoggingDefinitions;
use SAREhub\Commons\Logger\LoggerFactory;
use function DI\autowire;
use function DI\create;
use function DI\factory;
use function DI\get;

abstract class AmqpDefinitionsBase
{
    const ENTRY_MESSAGE_SENDER = "Amqp.MessageSender";

    public static function get(): array
    {
        return [
            AmqpConnectionOptions::class => factory(EnvAmqpConnectionOptionsProvider::class),
            AmqpConnectionService::class => static::connectionServiceDef(),
            static::getEnvSchemaProviderClass() => static::getEnvSchemaProviderDef(),
            AmqpEnvironmentSchemaCreator::class => create()
                ->constructor(get(AmqpEnvironmentManager::class), factory(static::getEnvSchemaProviderClass())),
            AmqpChannelWrapper::class => static::channelWrapperDef(),
            self::ENTRY_MESSAGE_SENDER => static::messageSenderDef()
        ];
    }

    protected static abstract function getEnvSchemaProviderDef();

    protected static abstract function getEnvSchemaProviderClass(): string;

    protected static function channelWrapperDef()
    {
        return factory(function (AmqpChannelWrapperProvider $provider, LoggerFactory $loggerFactory) {
            $wrapper = $provider->get();
            $wrapper->setLogger($loggerFactory->create("Amqp.Channel"));
            return $wrapper;
        });
    }

    protected static function messageSenderDef()
    {
        return ProcessorDefinitionHelper::pipeline([
            ProcessorDefinitionHelper::headerAppender([
                AmqpMessageHeaders::EXCHANGE => static::getMessageSenderExchange(),
            ]),
            ProcessorDefinitionHelper::filter(function (Exchange $exchange) {
                return $exchange->getIn()->getBody() instanceof Event;
            }, ProcessorDefinitionHelper::pipeline([
                ProcessorDefinitionHelper::marshal(get(RawEventDataFormat::class)),
                ProcessorDefinitionHelper::marshal(get(JsonDataFormat::class))
            ])),
            get(AmqpProducer::class)
        ]);
    }

    protected abstract static function getMessageSenderExchange(): string;

    protected static function connectionServiceDef()
    {
        return BasicLoggingDefinitions::inject(autowire(AmqpConnectionService::class), "Amqp.ConnectionService");
    }
}
