<?php

declare(strict_types=1);

namespace zejbm\shared\Domain\Message;

final class MessageId
{
    /**
     * @var string
     */
    private $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public function asString(): string
    {
        return $this->id;
    }

    public static function fromString(string $id): MessageId
    {
        return new self($id);
    }
}