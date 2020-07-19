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
use App\Common\Dao\SysUserDao;
use App\Common\Dao\SysUserRoleDao;
use App\Constants\Constants;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Listener\DeleteListenerEvent;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;

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
     * @Inject
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @Inject()
     * @var SysUserDao
     */
    private $sysUserDao;

    /**
     * 菜单导航,权限信息
     * @param int $user_id
     * @return array
     */
    public function getMenuNav(int $user_id): array
    {
        //@Cacheable(prefix="sys_menu", ttl=7200, listener="sys-menu-update")
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
        $orderBy = ['order_num' => 'asc'];
        $menu_category = $this->sysMenuDao->getDataByWhereForSelect([
            'parent_id' => 0,
            'type' => 0,
            'menu_id' => ['in', implode(',', $menu_ids)]
        ], true, ['menu_id as menuId', 'parent_id as parentId', 'name', 'url', 'perms', 'type', 'icon', 'order_num as orderNum'], $orderBy);

        $menuList = [];
        foreach ($menu_category as $key => $value) {
            //$model = $this->sysMenuDao->find($value['menuId']);
            $menus = $this->sysMenuDao->getDataByWhereForSelect([
                'parent_id' => $value['menuId'],
                'type' => 1,
                'menu_id' => ['in', implode(',', $menu_ids)]
            ], true, ['menu_id as menuId', 'parent_id as parentId', 'name', 'url', 'perms', 'type', 'icon', 'order_num as orderNum'], $orderBy);
            $value['list'] = $menus;
            $menuList[] = $value;
        }

        $permissionArrs = $this->sysMenuDao->getDataByWhereForSelect([
            'menu_id' => ['in', implode(',', $menu_ids)]
        ], true, ['*'], $orderBy);
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

    /**
     * 获取Menu列表
     * @param $user_id
     * @return array
     */
    public function getSysMenuList($user_id)
    {
        if ($user_id != Constants::SYS_ADMIN_ID) {
            $role_ids = $this->sysUserRoleDao->pluck(['user_id', $user_id], ['role_id']);
            $datas = $this->sysRoleMenuDao->getDataByWhereForSelect(['role_id' => ['in', implode(',', $role_ids)]], true);
        } else {
            $datas = $this->sysRoleMenuDao->getDataByWhereForSelect([], true);
        }

        if (empty($datas)) {
            return [];
        }
        $menu_ids = array_column($datas, 'menu_id');
        $menu_ids = array_unique($menu_ids);

        $l_feilds = "l.menu_id as menuId,l.parent_id as parentId,l.name,l.url,l.perms,l.type,l.icon,l.order_num as orderNum";
        $r_feilds = "r.name as parentName";
        $sys_menus = $this->sysMenuDao->selfJoinSelf($menu_ids, $l_feilds, $r_feilds);
        return $sys_menus;
    }

    /**
     * 删除菜单
     * @param $id
     * @return bool|int
     */
    public function getSysMenuDelete($id)
    {
        $hasParent = $this->sysMenuDao->getDataByWhereForSelect(['parent_id' => $id]);
        if (!empty($hasParent)) {
            return -1;
        }
        $sysUsers = $this->sysUserDao->getDataByWhereForSelect([], true);
        Db::beginTransaction();
        try {
            $this->sysMenuDao->deleteByWhere(['menu_id' => $id]);
            $this->sysRoleMenuDao->deleteByWhere(['menu_id' => $id]);
            Db::commit();
            foreach ($sysUsers as $sysUser) {
                $this->dispatcher->dispatch(new DeleteListenerEvent('sys-menu-update', [$sysUser['user_id']])); //清理cacheable生成的缓存
            }
            return true;
        } catch (\Throwable $ex) {
            Db::rollBack();
            return false;
        }
    }
}