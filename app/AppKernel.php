<?php

namespace OpenEuropa;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use OpenEuropa\DrupalSiteMigration\DependencyInjection\CompilerPass\CollectCommandsCompilerPass;

final class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../config/parameters.yml');
        $loader->load(__DIR__ . '/../config/services.yml');
    }

    /**
     * {@inheritdoc}
     */
    protected function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new CollectCommandsCompilerPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return __DIR__ . '/../var/cache/' . $this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return __DIR__ . '/../var/logs';
    }
}
