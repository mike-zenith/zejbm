<?php

declare(strict_types=1);

namespace zejbm\consumer\Domain\Message\Processor;

use zejbm\shared\Domain\Message\Message;
use zejbm\shared\Domain\Message\Processor\Processor;
use zejbm\shared\Domain\Message\StateChanger;

final class DistributedRandomProcessor implements Processor
{
    /**
     * @var RandomBooleanGenerator
     */
    private $randomBooleanGenerator;

    public function __construct(RandomBooleanGenerator $randomBooleanGenerator) {
        $this->randomBooleanGenerator = $randomBooleanGenerator;
    }

    public function __invoke(Message $message, StateChanger $changer): void {
        $this->randomBooleanGenerator->generate()
            ? $changer->accept()
            : $changer->reject();
    }
}