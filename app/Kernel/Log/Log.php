<?php
/**
 * Created by PhpStorm.
 * User: derek
 * Date: 2019/10/20
 * Time: 22:34
 */

namespace App\Kernel\Log;

use Hyperf\Utils\ApplicationContext;

class Log
{
    public static function get(string $name = 'app')
    {
        return ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get($name);
    }
}