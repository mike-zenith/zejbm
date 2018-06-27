<?php

declare(strict_types=1);

namespace zejbm\producer\UI\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class Action implements MiddlewareInterface
{
    /**
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $method;

    abstract protected function action(ServerRequestInterface $request): ResponseInterface;

    private function isActionable(ServerRequestInterface $request): bool {
        return $request->getRequestTarget() === $this->uri
            && $request->getMethod() === $this->method;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        if ($this->isActionable($request)) {
            return $this->action($request);
        }

        return $handler->handle($request);
    }

}