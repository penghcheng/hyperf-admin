<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\Constants;
use App\Kernel\Http\Response;
use App\Service\Instance\JwtInstance;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
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

    /**
     * @Inject()
     * @var Response
     */
    private $respone;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $request = $this->container->get(RequestInterface::class);
        $token = $request->getHeaderLine(Constants::TOKEN);

        $uri = $request->getRequestUri();
        $urIs = explode('/', $uri);

        $perms = ""; // 权限标识
        if (count($urIs) >= 5) {
            $perms = $urIs[2] . ":" . $urIs[3] . ":" . $urIs[4];
        } else {
            $perms = $urIs[2] . ":" . $urIs[3];
        }

        echo "perms:" . $perms . '-' . date("Y-m-d h:i:s") . PHP_EOL;

        if (!empty($token)) {
            JwtInstance::instance()->decode($token);
        } elseif (env('APP_DEBUG', false) === true) {
            JwtInstance::instance()->id = 1;
        }

        return $handler->handle($request);
    }
}