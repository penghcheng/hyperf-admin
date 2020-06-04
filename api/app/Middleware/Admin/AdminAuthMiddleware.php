<?php

declare(strict_types=1);

namespace App\Middleware\Admin;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Model\User;
use App\Service\UserService;
use Hyperf\Utils\Context;
use Phper666\JWTAuth\JWT;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;

class AdminAuthMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var HttpResponse
     */
    protected $response;
    protected $prefix = 'Bearer';
    protected $jwt;

    public function __construct(HttpResponse $response, JWT $jwt)
    {
        $this->response = $response;
        $this->jwt = $jwt;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $isValidToken = false;
        $token = $request->getHeader('Authorization')[0] ?? '';
        if (strlen($token) > 0) {
            $token = ucfirst($token);
            $arr = explode($this->prefix . ' ', $token);
            $token = $arr[1] ?? '';
            try {
                if (strlen($token) > 0 && $this->jwt->checkToken()) {
                    $isValidToken = true;
                }
            } catch (\Exception $e) {
                throw new BusinessException(ErrorCode::TOKEN_INVALID, '对不起，token验证没有通过');
            }
        }

        if ($isValidToken) {
            $jwtData = $this->jwt->getParserData();

            //更改上下文，写入用户信息
            //User模型自行创建

            /*$userService = di(UserService::class);
            $user = $userService->user($jwtData['user_id']);
            if (empty($user)) {
                throw new BusinessException(ErrorCode::TOKEN_INVALID, '对不起，token验证没有通过');
            }*/

            $request = Context::get(ServerRequestInterface::class);
            $request = $request->withAttribute('user', $jwtData);
            Context::set(ServerRequestInterface::class, $request);

            return $handler->handle($request);
        }

        throw new BusinessException(ErrorCode::TOKEN_INVALID, '对不起，token验证没有通过');
    }
}
