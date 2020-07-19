<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/5
 * Time: 9:52
 */

namespace App\Service;


use App\Common\Dao\SysUserDao;
use App\Constants\Constants;
use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use Hyperf\Di\Annotation\Inject;

class SysUserService extends BaseService
{

    /**
     * @Inject()
     * @var SysUserDao
     */
    private $sysUserDao;

    /**
     * 登录
     * @param string $username 用户名
     * @param string $password 密码
     * @return array
     */
    public function login(string $username, string $password)
    {
        $sys_user = $this->sysUserDao->getDataByWhereForSelect(['username' => $username], false);

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
        return $this->sysUserDao->findForSelect($user_id, $select);
    }

    /**
     * 根据管理员id查找
     * @param $user_id
     * @param bool $useCache
     * @return array
     */
    public function find($user_id, $useCache = false)
    {
        return $this->sysUserDao->find($user_id, $useCache);
    }

    /**
     * 管理员管理list
     * @param int $user_id
     * @param string $username
     * @param int $pageSize
     * @param int $currPage
     * @return array
     */
    public function getSysUserList(int $user_id, string $username, int $pageSize = 10, int $currPage = 1): array
    {
        $where = [];
        if (!empty($username)) {
            $where['username'] = ['like', "'%" . $username . "%'"];
        }
        if ($user_id != Constants::SYS_ADMIN_ID) {
            $where['create_user_id'] = $user_id;
        }

        $list = $this->sysUserDao->paginator($where, $pageSize, $currPage,
            ['user_id as userId', 'username', 'status', 'salt', 'password', 'mobile', 'email', 'create_user_id as createUserId', 'create_time as createTime']
        );

        return $list;
    }
}