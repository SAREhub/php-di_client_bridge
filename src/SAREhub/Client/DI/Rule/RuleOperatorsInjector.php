<?php


namespace SAREhub\Client\DI\Rule;


use Hoa\Ruler\Visitor\Asserter;

interface RuleOperatorsInjector
{
    public function injectToAsserter(Asserter $asserter): void;
}
