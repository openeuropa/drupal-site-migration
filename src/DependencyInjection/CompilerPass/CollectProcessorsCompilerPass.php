<?php

namespace OpenEuropa\DrupalSiteMigration\DependencyInjection\CompilerPass;

use OpenEuropa\DrupalSiteMigration\Processor\ProcessorInterface;
use OpenEuropa\DrupalSiteMigration\ProcessorManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class CollectProcessorsCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        $applicationDefinition = $containerBuilder->getDefinition(ProcessorManager::class);

        foreach ($containerBuilder->getDefinitions() as $name => $definition) {
            if (is_a($definition->getClass(), ProcessorInterface::class, true)) {
                $applicationDefinition->addMethodCall('add', [new Reference($name)]);
            }
        }
    }
}
