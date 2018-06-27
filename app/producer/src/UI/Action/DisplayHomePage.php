<?php

declare(strict_types=1);

namespace zejbm\producer\UI\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;

final class DisplayHomePage extends Action
{
    protected $uri = '/';
    protected $method = 'GET';

    public function action(ServerRequestInterface $request): ResponseInterface {
        return new HtmlResponse(
            file_get_contents(
                dirname(__FILE__) . '/../resources/view/index.html'
            ),
            200
        );
    }
}