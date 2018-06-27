<?php

declare(strict_types=1);

namespace zejbm\producer\UI\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use zejbm\producer\Application\UseCase\Message\SendRandomMessageToQueue as SendRandomMessageToQueueService;

final class SendRandomMessageToQueue extends Action
{
    protected $uri = '/messages';
    protected $method = 'POST';

    /**
     * @var SendRandomMessageToQueueService
     */
    private $sendRandomMessageToQueueService;

    public function __construct(SendRandomMessageToQueueService $sendRandomMessageToQueue)
    {
        $this->sendRandomMessageToQueueService = $sendRandomMessageToQueue;
    }

    protected function action(ServerRequestInterface $request): ResponseInterface
    {
        ($this->sendRandomMessageToQueueService)();
        return (new Response())->withStatus(202);
    }
}