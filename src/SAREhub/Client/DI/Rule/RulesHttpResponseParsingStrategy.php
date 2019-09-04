<?php


namespace SAREhub\Client\DI\Rule;


use Psr\Http\Message\ResponseInterface;
use SAREhub\Commons\Misc\ArrayHelper;
use SAREhub\EasyECA\Rule\Definition\EventRuleGroupDefinitionFactory;
use SAREhub\EasyECA\Rule\Loader\Http\HttpResponseParsingStrategy;

class RulesHttpResponseParsingStrategy implements HttpResponseParsingStrategy
{
    /**
     * @var EventRuleGroupDefinitionFactory
     */
    private $definitionFactory;

    /**
     * @var string
     */
    private $groupIdKey;

    public function __construct(EventRuleGroupDefinitionFactory $definitionFactory, $groupIdKey = "campaignId")
    {
        $this->definitionFactory = $definitionFactory;
        $this->groupIdKey = $groupIdKey;
    }

    public function parse(ResponseInterface $response): array
    {
        $data = $this->extractRulesFromResponse($response);
        $data = (isset($data[$this->groupIdKey])) ? [$data] : $data;
        $definitions = [];
        foreach ($data as $groupConfig) {
            $groupDefinitions = $this->createEventRuleGroupDefinitions($groupConfig);
            $definitions = array_merge($definitions, $groupDefinitions);
        }
        return $definitions;
    }

    protected function extractRulesFromResponse(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true)["data"];
    }

    private function createEventRuleGroupDefinitions(array $config): array
    {
        $groupId = $config[$this->groupIdKey];
        $rulesGroups = $this->groupRulesByEventType($config["config"]["rules"]);
        $definitions = [];
        foreach ($rulesGroups as $eventType => $rules) {
            $definitions[] = $this->definitionFactory->create($eventType, [
                "id" => $groupId,
                "rules" => $rules
            ]);
        }
        return $definitions;
    }

    private function groupRulesByEventType(array $rules): array
    {
        return ArrayHelper::groupByKey($rules, "event");
    }
}
