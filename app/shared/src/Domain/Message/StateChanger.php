<?php

declare(strict_types=1);

namespace zejbm\shared\Domain\Message;

class StateChanger
{
    /**
     * @var callable
     */
    private $accept;
    /**
     * @var callable
     */
    private $reject;

    public function __construct(callable $accept, callable $reject) {
        $this->accept = $accept;
        $this->reject = $reject;
    }

    public function accept(): void {
        call_user_func($this->accept);
    }

    public function reject(): void {
        call_user_func($this->reject);
    }
}