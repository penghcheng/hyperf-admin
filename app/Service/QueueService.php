<?php
/**
 * Created by PhpStorm.
 * User: derek
 * Date: 2019/11/18
 * Time: 21:14
 */

namespace App\Service;


use Hyperf\AsyncQueue\Annotation\AsyncQueueMessage;
use Hyperf\Di\Annotation\Inject;

class QueueService extends Service
{
    /**
     * @Inject()
     * @var SysUserService
     */
    public $sysUserService;

    /**
     * 写入系统操作日志
     * @AsyncQueueMessage()
     */
    public function handleSysLog($params)
    {
        // 需要异步执行的代码逻辑
        $this->sysUserService->sysLogSave($params);
    }
}