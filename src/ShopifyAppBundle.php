<?php

namespace MattDunbar\ShopifyAppBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ShopifyAppBundle extends AbstractBundle
{
    /**
     * @param array<mixed> $config
     * @param ContainerConfigurator $containerConfigurator
     * @param ContainerBuilder $containerBuilder
     * @return void
     */
    public function loadExtension(
        array $config,
        ContainerConfigurator $containerConfigurator,
        ContainerBuilder $containerBuilder
    ): void {
        $containerConfigurator->import(__DIR__ . '/Resources/config/services.yaml');
        $containerConfigurator->import(__DIR__ . '/Resources/config/config.yaml');
    }
}
