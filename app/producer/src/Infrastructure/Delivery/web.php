<?php

declare(strict_types=1);

error_reporting(E_ALL);

require (dirname(__FILE__) . '/../../../../vendor/autoload.php');

use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Relay\Relay;

require 'container.php';

$middleware = array_keys($containerBuilder->findTaggedServiceIds('action'));
$middleware[] = function (ServerRequestInterface $request, RequestHandlerInterface $next) {

    error_log(sprintf('404: %s %s', $request->getMethod(), $request->getRequestTarget()));
    return (new Response())->withStatus(404);
};

array_unshift(
    $middleware,
    function (ServerRequestInterface $request, RequestHandlerInterface $next) {
        file_put_contents(
            'php://stdout',
            sprintf("\n\r%s %s",
                $request->getMethod(),
                $request->getRequestTarget()
            )
        );
        return $next->handle($request);
    }
);

$resolver = function ($entry) use ($containerBuilder) {
    if (is_string($entry)) {
        try {
            return $containerBuilder->get($entry);
        } catch (\Throwable $e) {
            error_log($e->getMessage());
        }
    }
    return $entry;
};

send(
    (new Relay($middleware, $resolver))->handle(ServerRequestFactory::fromGlobals())
);

function send(ResponseInterface $response)
{
    $http_line = sprintf('HTTP/%s %s %s',
        $response->getProtocolVersion(),
        $response->getStatusCode(),
        $response->getReasonPhrase()
    );
    header($http_line, true, $response->getStatusCode());

    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            header("$name: $value", false);
        }
    }
    $stream = $response->getBody();
    if ($stream->isSeekable()) {
        $stream->rewind();
    }

    while (!$stream->eof()) {
        echo $stream->read(1024 * 8);
    }
}


