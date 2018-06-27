<?php

declare(strict_types=1);

namespace zejbm\consumer\tests\UseCase;

use PHPUnit\Framework\TestCase;
use zejbm\consumer\Application\UseCase\Message\ConsumeAndProcessMessage;
use zejbm\shared\Domain\Message\Processor\Processor;
use zejbm\shared\Domain\Message\StateChanger;
use zejbm\shared\Domain\Message\Transport\Consumer;
use zejbm\shared\Domain\Message\Transport\Publisher;
use zejbm\shared\Domain\Message\Message;
use zejbm\shared\tests\fakes\FakeProcessor;

class ConsumeAndProcessMessageTest extends TestCase
{

    /**
     * @var ConsumeAndProcessMessage
     */
    private $subject;

    /**
     * @var Consumer
     */
    private $consumer;

    /**
     * @var Message
     */
    private $consumedMessage;

    /**
     * @var FakeProcessor
     */
    private $processor;

    /**
     * @var Publisher
     */
    private $publisher;

    public function setUp() {
        $this->consumedMessage = Message::createNew('test');

        $this->consumer = $this->createMock(Consumer::class);
        $this->consumer->method('consume')->willReturn($this->consumedMessage);

        $this->publisher = $this->createMock(Publisher::class);

        $this->processor = new FakeProcessor();

        $this->subject = new ConsumeAndProcessMessage(
            $this->consumer,
            $this->processor,
            $this->publisher
        );
    }

    public function testItConsumesMessage() {
        $this->consumer
            ->expects($this->once())
            ->method('consume')
            ->willReturn($this->consumedMessage);

        $this->subject->__invoke();
    }

    public function testItRePublishesWhenProcessingFailed() {
        $this->processor->callAccept = false;

        $this->publisher
            ->expects($this->once())
            ->method('publish')
            ->with($this->consumedMessage);

        $this->subject->__invoke();
    }

    public function testItDoesNotRepublishWhenProcessingFailedThreeTimes() {
        $this->processor->callAccept = false;

        $this->subject->__invoke();

        $this->publisher
            ->expects($this->once())
            ->method('publish')
            ->with($this->consumedMessage);
        $this->subject->__invoke();

        $this->publisher->expects($this->never())
            ->method('publish');
        $this->subject->__invoke();
    }

}