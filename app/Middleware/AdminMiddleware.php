<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\Constants;
use App\Kernel\Http\Response;
use App\Service\Instance\JwtInstance;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Logger\LoggerFactory;
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

    /**
     * @var LoggerFactory;
     */
    protected $logger;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get(StdoutLoggerInterface::class);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $request = $this->container->get(RequestInterface::class);
        $token = $request->getHeaderLine(Constants::TOKEN);

        $uri = $request->getRequestUri();
        $urIs = explode('/', $uri);

        if (count($urIs) >= 5) { // 权限标识
            $perms = $urIs[2] . ":" . $urIs[3] . ":" . $urIs[4];
        } else {
            $perms = $urIs[2] . ":" . $urIs[3];
        }

        $this->logger->notice(PHP_EOL . 'TIME:' . date("Y-m-d h:i:s") . PHP_EOL . "PERMS:" . $perms . PHP_EOL . "IP:" . $request->getServerParams()["remote_addr"]);

        if (env('APP_DEBUG', false) === true) {
            JwtInstance::instance()->id = 1;
            return $handler->handle($request);
        }

        if (empty($token)) {
            return $this->respone->error("token not null");
        }

        try {
            JwtInstance::instance()->decode($token);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            return $this->respone->error($e->getMessage());
        }

        return $handler->handle($request);
    }
}