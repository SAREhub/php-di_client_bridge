<?php


namespace SAREhub\Client\DI\Rule;


use GuzzleHttp\Client;
use SAREhub\Commons\Misc\EnvironmentHelper;
use SAREhub\Commons\Misc\InvokableProvider;
use SAREhub\EasyECA\Util\HttpGetRequestCommand;

class RulesGetRequestCommandProvider extends InvokableProvider
{
    const RULES_GET_REQUEST_URI = "RULES_GET_REQUEST_URI";

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get()
    {
        $uri = EnvironmentHelper::getRequiredVar(self::RULES_GET_REQUEST_URI);
        return new HttpGetRequestCommand($this->client, $uri);
    }
}
