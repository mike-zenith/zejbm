<?php

declare(strict_types=1);

namespace zejbm\consumer\Domain\Message\Processor;

interface RandomBooleanGenerator
{
    public function generate(): bool;
}