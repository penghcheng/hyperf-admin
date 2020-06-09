<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/9
 * Time: 10:00
 */

namespace App\Service;


use App\Common\Dao\SysMenuDao;
use App\Common\Dao\SysRoleMenuDao;
use App\Common\Dao\SysUserRoleDao;
use App\Constants\Constants;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Di\Annotation\Inject;

class SysMenuService extends BaseService
{
    /**
     * @Inject()
     * @var SysMenuDao
     */
    private $sysMenuDao;

    /**
     * @Inject()
     * @var SysUserRoleDao
     */
    private $sysUserRoleDao;

    /**
     * @Inject()
     * @var SysRoleMenuDao
     */
    private $sysRoleMenuDao;

    /**
     * 菜单导航,权限信息
     * @param int $user_id
     * @return array
     */
    /**
     * @Cacheable(prefix="sys_menu", ttl=7200, listener="sys-menu-update")
     */
    public function getMenuNav(int $user_id): array
    {
        //$this->dispatcher->dispatch(new DeleteListenerEvent('sys-menu-update', [$userId])); //清理cacheable生成的缓存

        if ($user_id != Constants::SYS_ADMIN_ID) {
            $userRoleIdArrs = $this->sysUserRoleDao->getDataByWhereForSelect(['user_id' => $user_id], true, ['role_id']);
            $role_ids = array_column($userRoleIdArrs, 'role_id');
            $datas = $this->sysRoleMenuDao->getDataByWhereForSelect(['role_id' => ['in', implode(',', $role_ids)]], true);
        } else {
            $datas = $this->sysMenuDao->getDataByWhereForSelect([], true);
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
            'menu_id' => ['in', implode(',', $menu_ids)]
        ], true, ['menu_id as menuId', 'parent_id as parentId', 'name', 'url', 'perms', 'type', 'icon', 'order_num as orderNum'], 'orderNum asc');

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