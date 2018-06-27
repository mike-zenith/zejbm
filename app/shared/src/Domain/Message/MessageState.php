<?php

declare(strict_types=1);

namespace zejbm\shared\Domain\Message;

final class MessageState
{
    const ACCEPTED = 'accepted';
    const REJECTED = 'rejected';
    const NEW = 'new';

    /**
     * @var string
     */
    private $state;

    private function __construct(string $state) {
        $this->state = $state;
    }

    public function asString(): string {
        return $this->state;
    }

    public static function fromString(string $state): self {
        assert(in_array($state, [self::ACCEPTED, MessageState::REJECTED, self::NEW]));
        return new self($state);
    }

    public static function asAccepted(): self {
        return new self(MessageState::ACCEPTED);
    }

    public static function asRejected(): self {
        return new self(MessageState::REJECTED);
    }

    public static function asNew(): self {
        return new self(MessageState::NEW);
    }

}