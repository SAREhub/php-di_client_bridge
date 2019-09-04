<?php


namespace SAREhub\Client\DI\Worker;


use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use SAREhub\Commons\Logger\BasicLoggingDefinitions;
use SAREhub\Commons\Secret\SecretValueProvider;
use SAREhub\DockerUtil\Container\ContainerFactory;

use SAREhub\DockerUtil\Secret\DockerSecretValueProvider;
use function DI\get;

class WorkerContainerFactory implements ContainerFactory
{
    /**
     * @return ContainerInterface
     * @throws \Exception
     */
    public function create(): ContainerInterface
    {
        $builder = new ContainerBuilder();
        $this->configureOptions($builder);
        $this->addDefinitions($builder);
        return $builder->build();
    }

    protected function configureOptions(ContainerBuilder $builder): void
    {
        $builder->useAnnotations(false)->useAutowiring(true);
    }

    protected function addDefinitions(ContainerBuilder $builder): void
    {
        $this->addUtilDefinitions($builder);
        $this->addWorkerDefinitions($builder);
    }

    protected function addUtilDefinitions(ContainerBuilder $builder): void
    {
        $builder->addDefinitions(BasicLoggingDefinitions::get());
        $builder->addDefinitions([
            SecretValueProvider::class => get(DockerSecretValueProvider::class)
        ]);
    }

    protected function addWorkerDefinitions(ContainerBuilder $builder): void
    {
        $builder->addDefinitions(WorkerDefinitions::get());
    }
}
