<?php

declare(strict_types=1);

namespace zejbm\producer\Application\UseCase\Message;

use zejbm\producer\Domain\Message\Factory\RandomMessageFactory;
use zejbm\shared\Domain\Message\Transport\Publisher;

final class SendRandomMessageToQueue
{
    /**
     * @var RandomMessageFactory
     */
    private $randomMessageFactory;

    /**
     * @var Publisher
     */
    private $transport;

    public function __construct(
        RandomMessageFactory $randomMessageFactory,
        Publisher $transport
    ) {
        $this->randomMessageFactory = $randomMessageFactory;
        $this->transport = $transport;
    }

    public function __invoke()
    {
        $message = $this->randomMessageFactory->create();
        $this->transport->publish($message);

        return $message;
    }
}