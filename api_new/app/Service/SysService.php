<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/12
 * Time: 16:27
 */

namespace App\Service;


use App\Constants\ErrorCode;
use App\Dao\SysUserDao;
use App\Exception\BusinessException;
use App\Formatter\SysUserFormatter;
use Hyperf\Di\Annotation\Inject;

class SysService extends Service
{

    /**
     * @Inject()
     * @var SysUserDao
     */
    private $sysUserDao;

    /**
     * 用户登录
     * @param string $username
     * @return mixed
     */
    public function login(string $username, string $password)
    {
        $sysUser = $this->sysUserDao->getUserByName($username);

        if (!password_verify($password, $sysUser->password)) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, '用户名或密码错误');
        }
        if ($sysUser->status != 1) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, '该用户禁止登陆');
        }
        return $sysUser;
    }

}