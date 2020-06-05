<?php

declare(strict_types=1);

namespace App\Middleware\Admin;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Model\User;
use App\Service\SysUserService;
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
    protected $jwt;

    public function __construct(HttpResponse $response, JWT $jwt)
    {
        $this->response = $response;
        $this->jwt = $jwt;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $isValidToken = false;
        $token = $request->getHeader('token')[0] ?? '';
        if (strlen($token) > 0) {
            try {
                if ($this->jwt->checkToken($token)) {
                    $isValidToken = true;
                }
            } catch (\Exception $e) {
                throw new BusinessException(ErrorCode::SERVER_ERROR, 'sorry，token no pass!');
            }
        }else{
            throw new BusinessException(ErrorCode::SERVER_ERROR, 'token not null');
        }

        if ($isValidToken) {
            $jwtData = $this->jwt->getParserData($token);
            //更改上下文，写入用户信息
            $userService = di()->get(SysUserService::class);
            $user = $userService->find($jwtData['user_id'],true);
            if (empty($user)) {
                throw new BusinessException(ErrorCode::SERVER_ERROR, 'sorry，token no pass.');
            }

            $request = Context::get(ServerRequestInterface::class);
            $request = $request->withAttribute('user', $jwtData);
            Context::set(ServerRequestInterface::class, $request);

            return $handler->handle($request);
        }

        throw new BusinessException(ErrorCode::TOKEN_INVALID, '对不起，token验证没有通过');
    }
}
