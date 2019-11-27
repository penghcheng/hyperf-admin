<?php
/**
 * Created by PhpStorm.
 * User: derek
 * Date: 2019/11/18
 * Time: 22:16
 */

namespace App\Task;


use Hyperf\Di\Annotation\Inject;

class FooTask
{
    /**
     * @Inject()
     * @var \Hyperf\Contract\StdoutLoggerInterface
     */
    private $logger;

    public function execute()
    {
        $this->logger->info("FooTask:" . date('Y-m-d H:i:s', time()));
    }
}