<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/5
 * Time: 9:52
 */

namespace App\Service;


use App\Common\Dao\SysMenuDao;
use App\Common\Dao\SysRoleMenuDao;
use App\Common\Dao\SysUserDao;
use App\Common\Dao\SysUserRoleDao;
use App\Constants\Constants;
use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Model\SysMenu;
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
     * 菜单导航,权限信息
     * @param int $user_id
     * @return array
     */
    public function getNemuNav(int $user_id): array
    {
        $sysMenuDao = di()->get(SysMenuDao::class);
        $sysUserRoleDao = di()->get(SysUserRoleDao::class);
        $sysRoleMenuDao = di()->get(SysRoleMenuDao::class);
        if ($user_id != Constants::SYS_ADMIN_ID) {
            $userRoleIdArrs = $sysUserRoleDao->getDataByWhereForSelect(['user_id' => $user_id], true, ['role_id']);
            $role_ids = array_column($userRoleIdArrs, 'role_id');
            $datas = $sysRoleMenuDao->getDataByWhereForSelect(['role_id' => ['in', implode(',', $role_ids)]], true);
        } else {
            $datas = $sysMenuDao->getDataByWhereForSelect([], true);
        }
        $menu_ids = array_column($datas, 'menu_id');
        $result = $this->getUserMenusPermissions($menu_ids);
        return $result;
    }

    /**
     * 获取菜单和权限
     * @param array $menu_ids
     * @return array
     */
    private function getUserMenusPermissions(array $menu_ids)
    {
        $sysMenuDao = di()->get(SysMenuDao::class);
        $menu_category = $sysMenuDao->getDataByWhereForSelect([
            'parent_id' => 0,
            'type' => 0,
            'menu_id' => ['in' , implode(',', $menu_ids)]
        ], true, ['menu_id as menuId','parent_id as parentId','name','url','perms','type','icon','order_num as orderNum'], 'orderNum asc');

        $menuList = [];
        foreach ($menu_category as $key => $value) {
            $model = $sysMenuDao->find($value['menuId']);
            $menus = $sysMenuDao->getDataByWhereForSelect([
                'parent_id' => $model['menu_id'],
                'type' => 1,
                'menu_id' => ['in', implode(',', $menu_ids)]
            ], true, ['*'], 'order_num asc');

            $model['list'] = $menus;
            $menuList[] = $model;
        }

        $permissionArrs = $sysMenuDao->getDataByWhereForSelect([
            'menu_id' => ['in', implode(',', $menu_ids)]
        ], true, ['*'], 'order_num asc');
        $permissionArrs = array_column($permissionArrs, 'perms');

        $permissions = [];
        foreach ($permissionArrs as $perms) {
            if (!empty($perms)) {
                if (explode(',', $perms) > 0) {
                    if (!empty($permissions)) {
                        $permissions = array_merge($permissions, explode(',', $perms));
                    } else {
                        $permissions = explode(',', $perms);
                    }
                } else {
                    $permissions [] = $perms;
                }
            }
        }

        $permissions = array_unique($permissions);

        $permArrays = [];
        foreach ($permissions as $key => $val) {
            $permArrays[] = $val;
        }

        // 默认存在的权限
        $allowPermissions = [
            'sys:menu:nav',
            'sys:user:info',
            'sys:user:password'
        ];
        $permArrays = array_merge($permArrays, $allowPermissions);
        return [$menuList, $permArrays];
    }
}