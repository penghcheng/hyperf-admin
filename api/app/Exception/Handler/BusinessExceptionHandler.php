<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/5/18
 * Time: 10:46
 */

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Common\Http\Response;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\Validation\ValidationException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class BusinessExceptionHandler extends ExceptionHandler
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->response = $container->get(Response::class);
        $this->logger = $container->get(StdoutLoggerInterface::class);
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        if ($throwable instanceof BusinessException) {
            $this->logger->warning(format_throwable($throwable));
            return $this->response->error($throwable->getCode(), $throwable->getMessage());
        }

        if ($throwable instanceof ValidationException) {
            $message = $throwable->validator->errors()->first();
            return $this->response->error(ErrorCode::COMMON_ERROR, $message);
        }

        $this->logger->error(format_throwable($throwable));
        return $this->response->error(ErrorCode::SERVER_ERROR, $throwable->getMessage());
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
