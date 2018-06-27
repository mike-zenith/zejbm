<?php

declare(strict_types=1);

namespace zejbm\shared\Domain\Message;

use Ramsey\Uuid\Uuid;
use zejbm\shared\Domain\Message\Processor\Processor;
use zejbm\shared\Infrastructure\Message\Transport\RabbitMQTransportModel;

final class Message
{
    /**
     * @var MessageId
     */
    private $id;

    /**
     * @var string
     */
    private $body;

    /**
     * @var MessageState
     */
    private $state;

    /**
     * @var int
     */
    private $rejectedCount;

    private function __construct(MessageId $id, string $body, MessageState $state, int $rejectedCount = 0)
    {
        $this->id = $id;
        $this->body = $body;
        $this->state = $state;
        $this->rejectedCount = $rejectedCount;
    }

    public function getBody(): string {
        return $this->body;
    }

    public function getId(): MessageId {
        return $this->id;
    }

    public function process(Processor $processor) {
        $processor(
            $this,
            new StateChanger(
                function() { $this->toAccepted(); },
                function() { $this->toRejected(); }
            )
        );
    }

    public function isRejected() {
        return $this->state == MessageState::asRejected();
    }

    public function getState(): MessageState {
        return $this->state;
    }

    public function getNumberOfTimesRejected(): int {
        return $this->rejectedCount;
    }

    private function toAccepted() {
        $this->state = MessageState::asAccepted();
    }

    private function toRejected() {
        $this->state = MessageState::asRejected();
        $this->rejectedCount ++;
    }

    public static function createNew(string $body): self {
        return new self(
            MessageId::fromString(Uuid::uuid4()->toString()),
            $body,
            MessageState::asNew()
        );
    }

    public static function fromTransportModel(RabbitMQTransportModel $model): self {
        return new self(
            MessageId::fromString($model->getId()),
            $model->getBody(),
            MessageState::fromString($model->getState()),
            $model->getRejectedCount()
        );
    }
}