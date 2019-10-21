<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Model\Dao;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Kernel\Log\Log;
use App\Model\SysMenu;
use App\Model\SysRoleMenu;
use App\Model\SysUser;
use App\Model\SysUserRole;
use App\Service\Formatter\SysMenuFormatter;
use App\Service\Formatter\SysRoleMenuFormatter;
use App\Service\Service;
use Hyperf\DbConnection\Db;

class SysUserDao extends Service
{
    /**
     * @param $user_id
     * @param bool $throw
     * @return null|SysUserDao
     */
    public function first($user_id, $throw = true)
    {
        $model = SysUser::query()->where('user_id', $user_id)->first();
        if (empty($model) && $throw) {
            throw new BusinessException(ErrorCode::USER_NOT_EXIST);
        }
        return $model;
    }

    /**
     * @param $username
     * @param bool $throw
     * @return mixed
     */
    public function getOne($username, $throw = true)
    {
        $model = SysUser::query()->where('username', $username)->first();
        if (empty($model) && $throw) {
            throw new BusinessException(ErrorCode::USER_NOT_EXIST);
        }
        return $model;
    }

    /**
     * 获取管理用户的role_id
     * @param $user_id
     * @param bool $throw
     * @return mixed
     */
    public function getUserRole($user_id, $throw = true)
    {

        $model = SysUserRole::query()->where('user_id', $user_id)->first();
        if (empty($model) && $throw) {
            throw new BusinessException(ErrorCode::NOTE_NOT_EXIST);
        }
        return $model;
    }

    /**
     * 获取菜单和权限
     * @param $role_id
     * @param $user_id
     * @return array
     */
    public function getUserMenusPermissions($role_id, $user_id)
    {
        if ($user_id == 1) {
            $datas = Db::select('SELECT * FROM sys_menu;');
        } else {
            $datas = Db::select("SELECT * FROM sys_role_menu where role_id=" . $role_id . ";");
        }

        $menu_ids = array_column($datas, 'menu_id');

        $menu_category = Db::select('SELECT * FROM sys_menu where  parent_id = 0 and type = 0 and menu_id in (' . implode(',', $menu_ids) . ') order by order_num asc;');

        $menuList = [];
        foreach ($menu_category as $key => $value) {
            $model = SysMenu::query()->where("menu_id", $value['menu_id'])->first();
            $format = SysMenuFormatter::instance()->base($model);

            $menus = Db::select('SELECT * FROM sys_menu where  parent_id = ' . $format['menuId'] . ' and type = 1 order by order_num asc;');

            $arr = [];
            foreach ($menus as $v) {
                $arr [] = SysMenuFormatter::instance()->arr($v);
            }
            $format['list'] = $arr;

            $menuList[] = $format;
        }

        $permissionArrs = Db::select('SELECT * FROM sys_menu where  menu_id in (' . implode(',', $menu_ids) . ') order by order_num asc;');
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

        return [$menuList, $permArrays];
    }
}
