<?php

declare(strict_types=1);

namespace zejbm\consumer\Application\UseCase\Message;

use zejbm\shared\Domain\Message\Processor\Processor;
use zejbm\shared\Domain\Message\Transport\Consumer;
use zejbm\shared\Domain\Message\Transport\Publisher;

final class ConsumeAndProcessMessage
{
    /**
     * @var Consumer
     */
    private $consumer;

    /**
     * @var Publisher
     */
    private $publisher;

    public function __construct(
        Consumer $consumer,
        Processor $processor,
        Publisher $publisher
    ) {
        $this->consumer = $consumer;
        $this->processor = $processor;
        $this->publisher = $publisher;
    }

    public function __invoke(): void {
        $message = $this->consumer->consume();
        $message->process($this->processor);

        if ($message->isRejected() && $message->getNumberOfTimesRejected() < 3) {
            $this->publisher->publish($message);
        }
    }
}