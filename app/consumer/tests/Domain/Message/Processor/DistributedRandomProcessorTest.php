<?php

declare(strict_types=1);

namespace zejbm\consumer\tests\Domain\Message\Processor;

use PHPUnit\Framework\TestCase;
use zejbm\consumer\Domain\Message\Processor\DistributedRandomProcessor;
use zejbm\consumer\Domain\Message\Processor\RandomBooleanGenerator;
use zejbm\shared\Domain\Message\Message;
use zejbm\shared\Domain\Message\StateChanger;

class FakeRandomBooleanGenerator implements RandomBooleanGenerator
{
    public $nextGeneratedValue = true;

    public function generate(): bool {
        return $this->nextGeneratedValue;
    }
}

class DistributedRandomProcessorTest extends TestCase
{

    /**
     * @var DistributedRandomProcessor
     */
    private $subject;

    /**
     * @var Message
     */
    private $testMessage;

    /**
     * @var bool|null
     */
    private $isStateAccepted;

    /**
     * @var StateChanger
     */
    private $stateChanger;

    /**
     * @var FakeRandomBooleanGenerator
     */
    private $randomBooleanGenerator;

    public function setUp() {
        $this->testMessage = Message::createNew('body11');

        $this->stateChanger = new StateChanger(
            function() { $this->isStateAccepted = true; },
            function() { $this->isStateAccepted = false; }
        );

        $this->randomBooleanGenerator = new FakeRandomBooleanGenerator();

        $this->subject = new DistributedRandomProcessor($this->randomBooleanGenerator);
    }

    public function testWhenRandomGeneratorReturnsTrueItAccepts() {
        $this->randomBooleanGenerator->nextGeneratedValue = true;
        $this->subject->__invoke($this->testMessage, $this->stateChanger);
        $this->assertTrue($this->isStateAccepted);
    }

    public function testWhenRandomGeneratorReturnsFalseItRejects() {
        $this->randomBooleanGenerator->nextGeneratedValue = false;
        $this->subject->__invoke($this->testMessage, $this->stateChanger);
        $this->assertFalse($this->isStateAccepted);
    }
}