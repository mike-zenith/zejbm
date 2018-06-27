<?php

declare(strict_types=1);

use zejbm\shared\Infrastructure\Delivery\ContainerBuilderFactory;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

$env = $_ENV['APP_ENV'] ?? 'test';

$containerBuilder = (new ContainerBuilderFactory())->create($env);

$loader = new XmlFileLoader(
    $containerBuilder,
    new FileLocator([
        dirname(__FILE__) . '/../resources/config/' . $env . '/'
    ])
);

$loader->load('services.xml');
