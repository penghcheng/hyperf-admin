<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Kernel\Http\Response;
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
            //return $this->response->error(ErrorCode::PARAMS_INVALID, $message);
            return $this->response->error(ErrorCode::SERVER_ERROR, $message);
        }

        $this->logger->error(format_throwable($throwable));
        return $this->response->error(ErrorCode::SERVER_ERROR, 'Server Error');
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
