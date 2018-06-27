<?php

declare(strict_types=1);

namespace zejbm\producer\tests\UseCase;

use PHPUnit\Framework\TestCase;
use zejbm\producer\Application\UseCase\Message\SendRandomMessageToQueue;
use zejbm\producer\Domain\Message\Factory\RandomMessageFactory;
use zejbm\shared\Domain\Message\Message;
use zejbm\shared\Domain\Message\Transport\Publisher;

class SendRandomMessageToQueueTest extends TestCase
{

    /**
     * @var SendRandomMessageToQueue
     */
    private $subject;

    /**
     * @var RandomMessageFactory
     */
    private $randomMessage;

    /**
     * @var Publisher
     */
    private $transport;

    public function setUp()
    {
        $this->randomMessage = $this->createMock(RandomMessageFactory::class);
        $this->transport = $this->createMock(Publisher::class);

        $this->subject = new SendRandomMessageToQueue(
            $this->randomMessage,
            $this->transport
        );
    }

    public function testItSavesARandomMessage()
    {
        $message = Message::createNew('merandom');
        $this->randomMessage->method('create')->willReturn($message);

        $this->transport
            ->expects($this->once())
            ->method('publish')
            ->with($this->equalTo($message));

        $this->subject->__invoke();
    }
}