<?php

declare(strict_types=1);

namespace zejbm\features\bootstrap;

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Symfony\Component\Process\Process;

class ConsumerContext implements Context
{
    use QueueConnectionTrait;
    /**
     * @var Process
     */
    private $consumer;

    /**
     * @var PHPCommandRunner
     */
    private $commandRunner;

    public function __construct(string $containerFile) {
        $this->commandRunner = new PHPCommandRunner();
        $this->populateConfigFromContainer($containerFile);
    }

    /**
     * @AfterScenario
     */
    public function stopConsumer(AfterScenarioScope $afterScenarioScope) {
        if ($this->consumer) {
            echo "\r\nOutput:\r\n";
            echo $this->consumer->getOutput();
            echo "\r\nErrors:\r\n";
            echo $this->consumer->getErrorOutput();
        }
    }

    /**
     * @Given I start the consumer
     */
    public function iStartTheConsumer() {
        $consumerWorker = dirname(__FILE__)
            . '/../../consumer/src/Infrastructure/Delivery/cli-worker.php';

        $command = sprintf(
            'APP_ENV=test %s %s',
            $this->commandRunner->getPhpExecutableCommand(),
            $consumerWorker
        );

        $this->consumer = $this->commandRunner->runPhpCommand($command);
    }

    /**
     * @When I wait max :maxWait sec until consumer finishes
     */
    public function iWaitMaxSecUntilConsumerFinishes(string $maxWait) {
        $elapsed = 0;
        while ($elapsed < (int) $maxWait) {
            $start = microtime(true);
            if (! $this->getNumberOfMessagesInQueue()) {
                break;
            }
            $elapsed += microtime(true) - $start;
        }
    }
}
