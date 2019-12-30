<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\Constants;
use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Kernel\Http\Response;
use App\Kernel\Util\JwtInstance;
use App\Service\SysService;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\HttpServer\Contract\RequestInterface;

class AdminMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var LoggerFactory;
     */
    protected $logger;

    /**
     * @Inject()
     * @var SysService
     */
    private $sysService;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get(StdoutLoggerInterface::class);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeaderLine(Constants::TOKEN);
        $uri = $request->getServerParams()['request_uri'];
        //$urIs = explode('/', $uri);

        if (empty($token)) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, 'token not null');
        }

        try {
            JwtInstance::instance()->decode($token);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            throw new BusinessException(ErrorCode::TOKEN_INVALID, $e->getMessage());
        }

        $accessUserId = JwtInstance::instance()->build()->getId();

        $allowPermissions = [];

        if ($accessUserId != Constants::SYS_ADMIN_ID) {
            [$menuList, $permissions] = $this->sysService->getMenuNav($accessUserId);
            if (!empty($perms)) {
                // 没有访问权限
                if (!in_array($perms, $allowPermissions) && !in_array($perms, $permissions)) {
                    throw new BusinessException(ErrorCode::SERVER_ERROR, Constants::PERMISSION_DENIED);
                }
            }
        }

        return $handler->handle($request);
    }
}