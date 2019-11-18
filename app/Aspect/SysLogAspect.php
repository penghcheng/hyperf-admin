<?php

declare(strict_types=1);

namespace App\Aspect;

use App\Annotation\SysLogAnnotation;
use App\Service\Instance\JwtInstance;
use App\Service\QueueService;
use App\Service\SysUserService;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Aop\AbstractAspect;
use Psr\Container\ContainerInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Di\Aop\ProceedingJoinPoint;

/**
 * @Aspect
 */
class SysLogAspect extends AbstractAspect
{
    protected $container;

    /**
     * @Inject()
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject()
     * @var SysUserService
     */
    public $sysUserService;

    /**
     * @Inject
     * @var QueueService
     */
    protected $queueService;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // 要切入的注解，具体切入的还是使用了这些注解的类，仅可切入类注解和类方法注解
    public $annotations = [
        SysLogAnnotation::class,
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // 切面切入后，执行对应的方法会由此来负责
        // $proceedingJoinPoint 为连接点，通过该类的 process() 方法调用原方法并获得结果
        // 在调用前进行某些处理

        $loginUser = JwtInstance::instance()->build()->getSysUser();

        $redis = $this->container->get(\Redis::class);
        $username = $redis->get(env('APP_NAME') . "_loginUserId:" . $loginUser->user_id);
        if (empty($username)) {
            $username = $redis->set(env('APP_NAME') . "_loginUserId:" . $loginUser->user_id, $loginUser->username);
        }

        $path = $this->request->getPathInfo();
        $params = $this->request->getQueryParams();
        $ip = $this->request->getServerParams()["remote_addr"];
        $startTime = microtime(true);
        $requestMethod = $proceedingJoinPoint->className . "\\" . $proceedingJoinPoint->methodName;
        $arguments = $proceedingJoinPoint->arguments;

        $result = $proceedingJoinPoint->process();

        $endTime = microtime(true);
        $subTime = ($endTime - $startTime) * 1000;
        $data = [
            'username' => $username,
            'operation' => $path,
            'method' => $requestMethod,
            'params' => json_encode($params).json_encode($arguments),
            'time' => number_format($subTime, 2),
            'ip' => $ip,
            'create_date' => date("Y-m-d h:i:s", time())
        ];

        // 传统调用写入操作日志
        //$this->sysUserService->sysLogSave($data);
        // 异步队列 写入操作日志
        $this->queueService->handleSysLog($data);

        // 在调用后进行某些处理
        return $result;
    }
}
