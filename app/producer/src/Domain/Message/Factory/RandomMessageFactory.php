<?php

namespace zejbm\producer\Domain\Message\Factory;

use zejbm\shared\Domain\Message\Message;

interface RandomMessageFactory
{
    public function create(): Message;
}