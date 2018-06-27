<?php

declare(strict_types=1);

namespace zejbm\shared\Infrastructure\Delivery;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class ContainerBuilderFactory
{
    public function __construct() {
    }

    public function create(string $environment): ContainerBuilder {

        $containerBuilder = new ContainerBuilder();

        $locator = new FileLocator([
            dirname(__FILE__) . '/../resources/'
        ]);

        $loader = new DelegatingLoader(new LoaderResolver([
            new GlobFileLoader($containerBuilder, $locator),
            new XmlFileLoader($containerBuilder, $locator)
        ]));

        $loader->load('config/' . $environment . '/*.xml', 'glob');

        return $containerBuilder;
    }
}