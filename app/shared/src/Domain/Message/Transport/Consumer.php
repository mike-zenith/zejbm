<?php

declare(strict_types=1);

namespace zejbm\shared\Domain\Message\Transport;

use zejbm\shared\Domain\Message\Message;

interface Consumer
{
    public function consume(): Message;
}