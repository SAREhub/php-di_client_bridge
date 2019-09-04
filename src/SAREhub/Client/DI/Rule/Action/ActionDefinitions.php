<?php


namespace SAREhub\Client\DI\Rule\Action;


use DI\Definition\Helper\DefinitionHelper;
use Psr\Container\ContainerInterface;
use SAREhub\EasyECA\Rule\Action\ActionDefinitionFactory;
use SAREhub\EasyECA\Rule\Action\ActionParser;
use SAREhub\EasyECA\Rule\Action\MultiActionProcessorFactory;
use SAREhub\EasyECA\Rule\Action\NopActionProcessorFactory;
use function DI\create;
use function DI\decorate;
use function DI\get;

class ActionDefinitions
{
    const ENTRY_ACTION_FACTORIES = "Rule.Action.Factories";
    const ENTRY_SUB_ACTION_PARSER = "Rule.Action.SubActionParser";

    public static function get(): array
    {
        return [
            self::ENTRY_SUB_ACTION_PARSER => create(ActionParser::class)->constructor([]),
            self::ENTRY_ACTION_FACTORIES => self::actionFactoriesDefinition(),
            ActionParser::class => decorate(self::actionParserDecorator())
        ];
    }

    protected static function actionParserDecorator(): callable
    {
        return function (ActionParser $parser, ContainerInterface $c) {
            $subActionParser = $c->get(self::ENTRY_SUB_ACTION_PARSER);
            foreach ($c->get(self::ENTRY_ACTION_FACTORIES) as $action => $factory) {
                $parser->addActionFactory($action, $factory);
                $subActionParser->addActionFactory($action, $factory);
            }
            return $parser;
        };
    }

    protected static function actionFactoriesDefinition(): array
    {
        return [
            "nop" => get(NopActionProcessorFactory::class),
            "multi" => self::multiActionFactoryDefinition(),
        ];
    }

    protected static function multiActionFactoryDefinition(): DefinitionHelper
    {
        return create(MultiActionProcessorFactory::class)
            ->constructor(get(self::ENTRY_SUB_ACTION_PARSER), get(ActionDefinitionFactory::class));
    }
}
