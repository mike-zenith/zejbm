<?php

declare(strict_types=1);

namespace zejbm\shared\Infrastructure\Message\Transport;

use zejbm\shared\Domain\Message\Message;
use zejbm\shared\Domain\Message\Transport\Consumer;
use zejbm\shared\Domain\Message\Transport\Publisher;

final class RabbitMQTransport implements Publisher, Consumer
{
    /**
     * @var RabbitMQPipe
     */
    private $pipe;

    public function __construct(RabbitMQPipe $pipe) {
        $this->pipe = $pipe;
    }

    public function publish(Message $message): void {
        $this->pipe->send(RabbitMQTransportModel::fromMessage($message));
    }

    public function consume(): Message {
        return $this->pipe->consume()->asMessage();
    }
}