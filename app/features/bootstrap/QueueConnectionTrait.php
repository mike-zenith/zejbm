<?php

declare(strict_types=1);

namespace zejbm\features\bootstrap;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

trait QueueConnectionTrait
{
    /**
     * @var AMQPStreamConnection
     */
    protected $connection;

    /**
     * @var ParameterBagInterface
     */
    protected $config;

    public function populateConfigFromContainer(string $containerFile) {
        require ($containerFile);
        $this->config = $containerBuilder->getParameterBag();
    }

    public function purgeQueue() {
        $channel = $this->connection->channel();
        $channel->queue_delete($this->config->get('queue.queue'));
        $channel->exchange_delete($this->config->get('queue.exchange'));
        $channel->close();
        $this->connection->close();
    }

    private function createQueueConnectionFromConfig(ParameterBagInterface $params): AMQPStreamConnection {
        return new AMQPStreamConnection(
            $params->get('queue.host'),
            $params->get('queue.port'),
            $params->get('queue.user'),
            $params->get('queue.pass')
        );
    }

    protected function getAMQPConnection(): AMQPStreamConnection {
        if (! $this->connection) {
            $this->connection = $this->createQueueConnectionFromConfig($this->config);
        }
        return $this->connection;
    }

    private function getNumberOfMessagesInQueue(): int {
        $connection = $this->getAMQPConnection();
        list(, $messageCount) = $connection->channel()->queue_declare(
            $this->config->get('queue.queue'),
            true
        );

        return $messageCount;
    }
}