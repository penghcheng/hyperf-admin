<?php
use Hyperf\Crontab\Crontab;

return [
    // 是否开启定时任务
    'enable' => false,
    // 通过配置文件定义的定时任务
    'crontab' => [
        (new Crontab())->setName('Foo')->setRule('*/10 * * * * *')->setCallback([App\Task\FooTask::class, 'execute'])->setMemo('这是一个示例的定时任务'),
    ],
];