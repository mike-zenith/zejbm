<?php

declare(strict_types=1);

namespace zejbm\shared\Infrastructure\Message\Transport;

use PhpAmqpLib\Message\AMQPMessage;
use zejbm\shared\Domain\Message\Message;
use zejbm\shared\Domain\Message\MessageId;

final class RabbitMQTransportModel
{
    /**
     * @var string
     */
    private $body;
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $state;

    /**
     * @var int
     */
    private $rejectedCount;

    private function __construct(
        string $body,
        string $id,
        string $state,
        int $rejectedCount
    ) {
        $this->body = $body;
        $this->id = $id;
        $this->state = $state;
        $this->rejectedCount = $rejectedCount;
    }

    public function getBody(): string {
        return $this->body;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getState(): string {
        return $this->state;
    }

    public function getRejectedCount(): int {
        return $this->rejectedCount;
    }

    public static function fromMessage(Message $message): RabbitMQTransportModel {
        return new self(
            $message->getBody(),
            $message->getId()->asString(),
            $message->getState()->asString(),
            $message->getNumberOfTimesRejected()
        );
    }

    public static function fromAMQPMessage(AMQPMessage $message): RabbitMQTransportModel {
        $parsedBody = json_decode($message->getBody());

        return new self(
            $parsedBody->body,
            (string) $message->get('message_id'),
            $parsedBody->state,
            $parsedBody->rejected_count
        );
    }

    public function asAMQPMessage(): AMQPMessage {
        return new AMQPMessage(
            json_encode([
                'body' => $this->body,
                'state' => $this->state,
                'rejected_count' => $this->rejectedCount
            ]),
            [
                'message_id' => $this->id
            ]
        );
    }

    public function asMessage(): Message {
        return Message::fromTransportModel($this);
    }
}