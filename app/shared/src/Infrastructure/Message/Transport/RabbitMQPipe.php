<?php

declare(strict_types=1);

namespace zejbm\shared\Infrastructure\Message\Transport;

use PhpAmqpLib\Connection\AMQPStreamConnection;

final class RabbitMQPipe
{

    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var string
     */
    private $exchangeName;

    /**
     * @var string
     */
    private $queueName;

    public function __construct(
        AMQPStreamConnection $connection,
        string $queueName,
        string $exchangeName
    ) {
        $this->connection = $connection;
        $this->exchangeName = $exchangeName;
        $this->queueName = $queueName;

        $channel = $this->connection->channel();
        $channel->queue_declare($queueName, false, false, false, true);
        $channel->exchange_declare($exchangeName, 'direct', false, false, true);
        $channel->queue_bind($queueName,$exchangeName);
        $channel->close();
    }

    public function send(RabbitMQTransportModel $model): void {
        $channel = $this->connection->channel();
        $channel->basic_publish($model->asAMQPMessage(), $this->exchangeName);
        $channel->close();
    }

    public function consume(): RabbitMQTransportModel {
        $channel = $this->connection->channel();
        while (! $message = $channel->basic_get($this->queueName));

        return RabbitMQTransportModel::fromAMQPMessage($message);
    }

    public function __destruct() {
        $this->connection->close();
    }
}