<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\Constants;
use App\Constants\ErrorCode;
use App\Kernel\Http\Response;
use App\Service\Instance\JwtInstance;
use App\Service\SysUserService;
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

    /**
     * @Inject
     * @var SysUserService
     */
    protected $sysUserService;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get(StdoutLoggerInterface::class);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $this->container->get(RequestInterface::class);
        $token = $request->getHeaderLine(Constants::TOKEN);
        if (empty($token)) {
            $token = $request->getQueryParams()['token'];
        }
        $uri = $request->getRequestUri();
        $urIs = explode('/', $uri);

        $perms = null;
        if (count($urIs) >= 5) { // 权限标识
            $perms = $urIs[2] . ":" . $urIs[3] . ":" . $urIs[4];
        }

        $this->logger->notice(PHP_EOL . 'TIME:' . date("Y-m-d h:i:s") . PHP_EOL . "PERMS:" . $perms . PHP_EOL . "IP:" . $request->getServerParams()["remote_addr"]);

        // 开发下默认的id为 1
        /*if (env('APP_DEBUG', false) === true) {
            JwtInstance::instance()->id = 1;
            return $handler->handle($request);
        }*/

        if (empty($token)) {
            return $this->respone->error("token not null");
        }

        try {
            JwtInstance::instance()->decode($token);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            return $this->respone->error($e->getMessage(), ErrorCode::TOKEN_INVALID);
        }

        $accessUserId = JwtInstance::instance()->build()->getId();

        $allowPermissions = [];

        if ($accessUserId != 1) {

            [$menuList, $permissions] = $this->sysUserService->getNemuNav($accessUserId);

            if (!empty($perms)) {
                // 没有访问权限
                if (!in_array($perms, $allowPermissions) && !in_array($perms, $permissions)) {
                    return $this->respone->error(Constants::PERMISSION_DENIED);
                }
            }
        }

        return $handler->handle($request);
    }
}