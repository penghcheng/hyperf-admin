<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/5
 * Time: 9:52
 */

namespace App\Service;


use App\Common\Dao\SysUserDao;
use App\Constants\ErrorCode;
use App\Exception\BusinessException;

class SysUserService extends BaseService
{

    /**
     * 登录
     * @param string $username 用户名
     * @param string $password 密码
     * @return array
     */
    public function login(string $username, string $password)
    {
        $sysUserDao = di()->get(SysUserDao::class);
        $sys_user = $sysUserDao->getDataByWhereForSelect(['username' => $username], false);

        if (empty($sys_user)) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, "用户名或密码错误");
        }

        if (!password_verify($password, $sys_user['password'])) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, "用户名或密码错误");
        }
        if ($sys_user['status'] != 1) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, "该用户禁止登陆");
        }

        return $sys_user;
    }

    /**
     * 根据管理员id/ids查找
     * @param $user_id
     * @param array $select
     * @return array
     */
    public function findForSelect($user_id, array $select)
    {
        $sysUserDao = di()->get(SysUserDao::class);
        return $sysUserDao->findForSelect($user_id, $select);
    }

    /**
     * 根据管理员id查找
     * @param $user_id
     * @param bool $useCache
     * @return array
     */
    public function find($user_id, $useCache = false)
    {
        $sysUserDao = di()->get(SysUserDao::class);
        return $sysUserDao->find($user_id, $useCache);
    }

}