<?php

declare(strict_types=1);

namespace zejbm\shared\tests\fakes;

use zejbm\shared\Domain\Message\Message;
use zejbm\shared\Domain\Message\Processor\Processor;
use zejbm\shared\Domain\Message\StateChanger;

class FakeProcessor implements Processor
{
    public $callAccept = true;

    public function __invoke(Message $message, StateChanger $changer): void {
        if ($this->callAccept) {
            $changer->accept();
        } else {
            $changer->reject();
        }
    }
}