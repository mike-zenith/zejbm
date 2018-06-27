<?php

declare(strict_types=1);

namespace zejbm\producer\Infrastructure\Message\Factory;

use zejbm\producer\Domain\Message\Factory\RandomMessageFactory as RandomMessageFactoryInterface;
use zejbm\shared\Domain\Message\Message;

final class RandomMessageFactory implements RandomMessageFactoryInterface
{
    public function create(): Message
    {
        return Message::createNew((string) rand(0, getrandmax()));
    }
}