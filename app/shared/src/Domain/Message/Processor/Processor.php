<?php

declare(strict_types=1);

namespace zejbm\shared\Domain\Message\Processor;

use zejbm\shared\Domain\Message\StateChanger;
use zejbm\shared\Domain\Message\Message;

interface Processor
{
    public function __invoke(Message $message, StateChanger $changer): void;
}