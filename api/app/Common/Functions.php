<?php
/**
 * 公共方法类
 * User: penghcheng
 * Date: 2020/5/18
 * Time: 11:29
 */

use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\JobInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\Utils\ApplicationContext;

/**
 * 获取Container
 */
if (!function_exists('di')) {
    /**
     * Finds an entry of the container by its identifier and returns it.
     * @param null|mixed $id
     * @return mixed|\Psr\Container\ContainerInterface
     */
    function di($id = null)
    {
        $container = ApplicationContext::getContainer();
        if ($id) {
            return $container->get($id);
        }
        return $container;
    }
}

if (!function_exists('service')) {

    /**
     * service
     * 获取服务类实例
     * @param $key
     * @return mixed
     */
    function service($key)
    {
        $key = ucfirst($key);
        $fileName = BASE_PATH . "/app/Service/{$key}.php";
        $className = "App\\Service\\{$key}";

        if (file_exists($fileName)) {
            return di($className);
        } else {
            throw new \RuntimeException("服务{$key}不存在，文件不存在！",\App\Constants\ErrorCode::SERVER_ERROR);
        }
    }
}

/**
 * 控制台日志
 */
if (!function_exists('stdLog')) {
    function stdLog()
    {
        return di()->get(StdoutLoggerInterface::class);
    }
}

/**
 * 文件日志
 */
if (!function_exists('logger')) {
    function logger($name = 'hyperf', $group = 'default')
    {
        return di()->get(LoggerFactory::class)->get($name, $group);
    }
}

/**
 * redis 客户端实例
 */
if (!function_exists('redis')) {
    function redis()
    {
        return di()->get(Hyperf\Redis\Redis::class);
    }
}

/**
 * 缓存实例 简单的缓存
 */
if (!function_exists('cache')) {
    function cache()
    {
        return di()->get(\Psr\SimpleCache\CacheInterface::class);
    }
}

if (!function_exists('format_throwable')) {
    /**
     * Format a throwable to string.
     * @param Throwable $throwable
     * @return string
     */
    function format_throwable(Throwable $throwable): string
    {
        return di()->get(FormatterInterface::class)->format($throwable);
    }
}

if (!function_exists('queue_push')) {
    /**
     * Push a job to async queue.
     */
    function queue_push(JobInterface $job, int $delay = 0, string $key = 'default'): bool
    {
        $driver = di()->get(DriverFactory::class)->get($key);
        return $driver->push($job, $delay);
    }
}

if (!function_exists('encryptWithSalt')) {
    /**
     * 加密
     * @param $password
     * @param $salt
     * @return string
     */
    function encryptWithSalt($str, $salt)
    {
        return md5(md5($str) . $salt);
    }
}

if (!function_exists('encrypt')) {
    /**
     * 加密函数
     *
     * @param string $str 加密前的字符串
     * @param string $key 密钥
     * @return string 加密后的字符串
     */
    function encrypt($str, $key = '')
    {
        $coded = '';
        $keylength = strlen($key);

        for ($i = 0, $count = strlen($str); $i < $count; $i += $keylength) {
            $coded .= substr($str, $i, $keylength) ^ $key;
        }

        return str_replace('=', '', base64_encode($coded));
    }
}

if (!function_exists('decrypt')) {
    /**
     * 解密函数
     *
     * @param string $str 加密后的字符串
     * @param string $key 密钥
     * @return string 加密前的字符串
     */
    function decrypt($str, $key = '')
    {
        $coded = '';
        $keylength = strlen($key);
        $str = base64_decode($str);

        for ($i = 0, $count = strlen($str); $i < $count; $i += $keylength) {
            $coded .= substr($str, $i, $keylength) ^ $key;
        }

        return $coded;
    }
}

/**
 * 校验密码复杂度
 */
if (!function_exists('valid_pass')) {
    function valid_pass($password)
    {
        //$r1 = '/[A-Z]/';  //uppercase
        $r2 = '/[A-z]/';  //lowercase
        $r3 = '/[0-9]/';  //numbers
        $r4 = '/[~!@#$%^&*()\-_=+{};:<,.>?]/';  // special char

        /*if (preg_match_all($r1, $candidate, $o) < 1) {
            $msg =  "密码必须包含至少一个大写字母，请返回修改！";
            return FALSE;
        }*/
        if (preg_match_all($r2, $password, $o) < 1) {
            $msg = "密码必须包含至少一个字母，请返回修改！";
            return ['code' => -1, 'msg' => $msg];
        }
        if (preg_match_all($r3, $password, $o) < 1) {
            $msg = "密码必须包含至少一个数字，请返回修改！";
            return ['code' => -1, 'msg' => $msg];
        }
        /*if (preg_match_all($r4, $candidate, $o) < 1) {
            $msg =  "密码必须包含至少一个特殊符号：[~!@#$%^&*()\-_=+{};:<,.>?]，请返回修改！";
            return FALSE;
        }*/
        if (strlen($password) < 8) {
            $msg = "密码必须包含至少含有8个字符，请返回修改！";
            return ['code' => -1, 'msg' => $msg];
        }
        return ['code' => 0, 'msg' => 'success'];
    }
}

/**
 * 检查手机号码格式
 * @param $mobile 手机号码
 */
if (!function_exists('check_mobile')) {
    function check_mobile($mobile)
    {
        if (preg_match('/1[3-9]\d{9}$/', $mobile) || preg_match('/000\d{8}$/', $mobile)) {
            return true;
        }
        return false;
    }
}

if (!function_exists('page')) {
    /**
     * 计算总页数等
     * @param int $pageSize
     * @param int $currPage
     * @param $totalCount
     * @return array
     */
    function page($totalCount, int $pageSize = 10, int $currPage = 1): array
    {
        if ($totalCount > 0) {
            $totalPage = ceil($totalCount / $pageSize);
        } else {
            $totalPage = 0;
        }

        if ($currPage <= 0 || $currPage > $totalPage) {
            $currPage = 1;
        }

        $startCount = ($currPage - 1) * $pageSize;
        return array($totalPage, $startCount);
    }
}