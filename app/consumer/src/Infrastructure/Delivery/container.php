<?php

declare(strict_types=1);

use zejbm\shared\Infrastructure\Delivery\ContainerBuilderFactory;

$env = $_ENV['APP_ENV'] ?? 'test';

$containerBuilder = (new ContainerBuilderFactory())->create($env);

$loader = new \Symfony\Component\DependencyInjection\Loader\XmlFileLoader(
    $containerBuilder,
    new \Symfony\Component\Config\FileLocator([
        dirname(__FILE__) . '/../resources/config/' . $env . '/'
    ])
);

$loader->load('services.xml');
$loader->load('parameters.xml');