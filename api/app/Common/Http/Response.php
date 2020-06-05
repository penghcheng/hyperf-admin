<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/5/18
 * Time: 11:28
 */

namespace App\Common\Http;

use App\Constants\ErrorCode;
use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class Response
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ResponseInterface
     */
    protected $response;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->response = $container->get(ResponseInterface::class);
    }

    /**
     * @param $data
     * @return PsrResponseInterface
     */
    public function json($data)
    {
        return $this->response->json($data);
    }

    /**
     * @param $data
     * @param $message
     * @return PsrResponseInterface
     */
    public function success($data = [], $message='success')
    {
        $data = array_merge([
            'code' => 0,
            'msg' => $message
        ], $data);

        return $this->response->json($data);
    }

    /**
     * @param string $message
     * @param int $code
     * @return PsrResponseInterface
     */
    public function error($message = '',$code = ErrorCode::SERVER_ERROR)
    {
        return $this->response->json([
            'code' => $code,
            'msg' => $message,
        ]);
    }

    public function redirect($url, $status = 302)
    {
        return $this->response()
            ->withAddedHeader('Location', (string)$url)
            ->withStatus($status);
    }

    public function cookie(Cookie $cookie)
    {
        $response = $this->response()->withCookie($cookie);
        Context::set(PsrResponseInterface::class, $response);
        return $this;
    }

    /**
     * @return \Hyperf\HttpMessage\Server\Response
     */
    public function response()
    {
        return Context::get(PsrResponseInterface::class);
    }
}
