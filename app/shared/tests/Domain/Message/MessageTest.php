<?php

declare(strict_types=1);

namespace zejbm\shared\tests\Domain\Message;

use PHPUnit\Framework\TestCase;
use zejbm\shared\Domain\Message\Message;
use zejbm\shared\Domain\Message\MessageState;
use zejbm\shared\tests\fakes\FakeProcessor;

class MessageTest extends TestCase
{
    /**
     * @var Message
     */
    private $subject;

    /**
     * @var FakeProcessor
     */
    private $processor;

    public function setUp() {
        $this->subject = Message::createNew('body');

        $this->processor = new FakeProcessor();
    }

    public function testMessageHasNewStatus() {
        $this->assertEquals(
            $this->subject->getState(),
            MessageState::asNew()
        );
    }

    public function testMessageOnlyHaveOneStatus() {
        $state = $this->subject->getState();

        foreach([MessageState::asRejected(), MessageState::asAccepted()] as $checkAgainst) {
            $this->assertNotEquals($state, $checkAgainst);
        }
    }

    public function testProcessorCanChangeMessageStatus() {
        $this->processor->callAccept = true;
        $this->subject->process($this->processor);
        $this->assertEquals(
            $this->subject->getState(),
            MessageState::asAccepted()
        );

        $this->processor->callAccept = false;
        $this->subject->process($this->processor);
        $this->assertEquals(
            $this->subject->getState(),
            MessageState::asRejected()
        );
    }

    public function testRejectedProcessCountStoredInModel() {
        $this->processor->callAccept = false;

        $this->subject->process($this->processor);
        $this->subject->process($this->processor);
        $this->assertEquals(
            2,
            $this->subject->getNumberOfTimesRejected()
        );
    }

    public function testAcceptingARejectedMessageDoesNotResetRejectCount() {
        $this->processor->callAccept = false;

        $this->subject->process($this->processor);
        $this->subject->process($this->processor);

        $this->processor->callAccept = true;
        $this->subject->process($this->processor);
        $this->assertEquals(
            2,
            $this->subject->getNumberOfTimesRejected()
        );
    }
}