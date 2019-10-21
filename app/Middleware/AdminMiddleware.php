<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\Constants;
use App\Service\Instance\JwtInstance;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AdminMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeaderLine(Constants::TOKEN);

        if (! empty($token)) {
            JwtInstance::instance()->decode($token);
        } elseif (env('APP_DEBUG', false) === true) {
            JwtInstance::instance()->id = 1;
        }

        return $handler->handle($request);
    }
}