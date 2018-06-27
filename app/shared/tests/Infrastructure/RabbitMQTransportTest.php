<?php

declare(strict_types=1);

namespace zejbm\shared\tests\Infrastructure;

use zejbm\shared\Domain\Message\Transport\Publisher;
use zejbm\shared\Infrastructure\Message\Transport\RabbitMQPipe;
use zejbm\shared\Infrastructure\Message\Transport\RabbitMQTransport;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;
use zejbm\shared\Domain\Message\Message;
use zejbm\shared\Infrastructure\Message\Transport\RabbitMQTransportModel;
use zejbm\shared\tests\fakes\FakeProcessor;

class RabbitMQTransportTest extends TestCase
{
    const QUEUE_NAME = 'infra_test';
    const EXCHANGE_NAME = 'infra_test_exchange';
    /**
     * @var RabbitMQTransport
     */
    private $subject;

    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var Message
     */
    private $testMessage;

    public function setUp() {
        $this->connection = new AMQPStreamConnection(
            'rabbitmq',
            5672,
            'local',
            'local'
        );

        $this->testMessage = Message::createNew('body123');

        $this->subject = new RabbitMQTransport(
            new RabbitMQPipe($this->connection, self::QUEUE_NAME, self::EXCHANGE_NAME)
        );
    }

    public function tearDown() {
        $channel = $this->connection->channel();
        $channel->queue_delete(self::QUEUE_NAME);
        $channel->exchange_delete(self::EXCHANGE_NAME);
        $channel->close();
        $this->connection->close();
    }

    public function testItIsAPublisher() {
        $this->assertInstanceOf(Publisher::class, $this->subject);
    }

    public function testItSendsMessageToQueue() {
        $this->subject->publish($this->testMessage);

        $channel = $this->connection->channel();
        list (, $messageCount) = $channel->queue_declare(self::QUEUE_NAME, true);
        $channel->close();

        $this->assertSame(1, $messageCount);
    }

    public function testItTransportsSameMessageToQueue() {
        $this->subject->publish($this->testMessage);

        $channel = $this->connection->channel();
        $message = $channel->basic_get(self::QUEUE_NAME);
        $channel->close();

        $this->assertEquals(
            $this->testMessage,
            RabbitMQTransportModel::fromAMQPMessage($message)->asMessage()
        );
    }

    public function testItConsumesPublishedMessage() {
        $this->subject->publish($this->testMessage);

        $this->assertEquals(
            $this->testMessage,
            $this->subject->consume()
        );
    }

    public function testItTransportsMessageStatus() {
        $processor = new FakeProcessor();
        $processor->callAccept = false;
        $this->testMessage->process($processor);

        $this->subject->publish($this->testMessage);

        $this->assertEquals(
            $this->testMessage->getState(),
            $this->subject->consume()->getState()
        );
    }

    public function testItTransportsMessageRejectCount() {
        $processor = new FakeProcessor();
        $processor->callAccept = false;
        $this->testMessage->process($processor);
        $this->testMessage->process($processor);

        $expectedRejectCount = $this->testMessage->getNumberOfTimesRejected();
        $this->subject->publish($this->testMessage);

        $this->assertEquals(
            $expectedRejectCount,
            $this->subject->consume()->getNumberOfTimesRejected()
        );
    }

}