<?php
/**
 * Created by PhpStorm.
 * User: derek
 * Date: 2019/10/20
 * Time: 22:34
 */

namespace App\Kernel\Log;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Logger\LoggerFactory;

class Log
{
    /**
     * 文件日志
     * @param string $name
     * @return \Psr\Log\LoggerInterface
     */
    public static function get(string $name = 'app')
    {
        return di()->get(LoggerFactory::class)->get($name);
    }

    /**
     * 控制台日志
     */
    public static function stdLog()
    {
        return di()->get(StdoutLoggerInterface::class);
    }
}