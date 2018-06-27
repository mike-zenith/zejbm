<?php

declare(strict_types=1);

error_reporting(E_ALL);

require (dirname(__FILE__) . '/../../../../vendor/autoload.php');

require 'container.php';

/**
 * @var \zejbm\consumer\Application\UseCase\Message\ConsumeAndProcessMessage $consumer
 */
$consumer = $containerBuilder->get('Application\UseCase\Message\ConsumeAndProcessMessage');

while(true) {
    $consumer();
}