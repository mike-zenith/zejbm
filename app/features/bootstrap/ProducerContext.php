<?php

declare(strict_types=1);

namespace zejbm\features\bootstrap;

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Symfony\Component\Process\Process;

class ProducerContext implements Context
{

    const WEB_HOST = 'localhost';
    const WEB_PORT = 8000;
    /**
     * @var Process
     */
    private $webServer;

    /**
     * @var PHPCommandRunner
     */
    private $commandRunner;

    public function __construct() {
        $this->commandRunner = new PHPCommandRunner();
    }

    /**
     * @BeforeScenario
     */
    public function startWebServer(BeforeScenarioScope $beforeScenario) {

        $docRoot = dirname(__FILE__) . '/../../producer/src/Infrastructure/Delivery/';
        $router = $docRoot . 'web.php';

        $command = sprintf(
            'APP_ENV=test %s -S %s -t %s %s',
            $this->commandRunner->getPhpExecutableCommand(),
            implode(':', [ self::WEB_HOST, self::WEB_PORT ]),
            $docRoot,
            $router
        );

        echo 'Starting web server';

        $this->webServer = $this->commandRunner->runPhpCommand(
            $command,
            function (Process $process) {
                $isRunning = $this->isWebServerProcessReady(
                    $process,
                    self::WEB_HOST,
                    self::WEB_PORT
                );

                echo '.';
                if (! $isRunning) {
                    usleep(2000);
                }

                return $isRunning;
            }
         );
    }

    private function isWebServerProcessReady(
        Process $process,
        string $host,
        int $port
    ): bool {
        return $process->isStarted() && @fsockopen($host, $port);
    }

    /**
     * @AfterScenario
     */
    public function stopWebServer(AfterScenarioScope $afterScenario) {
        if ($this->webServer) {
            echo 'Stopping web server';
            $this->webServer->stop();

            echo "\r\nOutput:\r\n";
            echo $this->webServer->getOutput();
            echo "\r\nErrors:\r\n";
            echo $this->webServer->getErrorOutput();

            while(
                $this->isWebServerProcessReady(
                    $this->webServer,
                    self::WEB_HOST,
                    self::WEB_PORT
                )
            );
        }
    }
}
