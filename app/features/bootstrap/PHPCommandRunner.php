<?php

declare(strict_types=1);

namespace zejbm\features\bootstrap;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class PHPCommandRunner
{

    public function getPhpExecutableCommand(): string {
        $phpExecutableFinder = new ExecutableFinder();
        return sprintf('exec %s', $phpExecutableFinder->find('php'));
    }

    public function runPhpCommand(string $command, callable $isRunning = null): Process {
        $isRunning = $isRunning ?? function (Process $process) { return $process->isRunning(); };

        $process = new Process(escapeshellcmd($command));
        $process->start();

        while (!$isRunning($process));

        return $process;
    }
}