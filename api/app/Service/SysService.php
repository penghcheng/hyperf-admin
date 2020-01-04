<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/12
 * Time: 16:27
 */

namespace App\Service;


use App\Constants\Constants;
use App\Constants\ErrorCode;
use App\Dao\SysUserDao;
use App\Exception\BusinessException;
use App\Formatter\SysMenuFormatter;
use App\Formatter\SysUserFormatter;
use App\Model\SysMenu;
use App\Model\SysRoleMenu;
use App\Model\SysUser;
use Hyperf\DbConnection\Db;
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

    /**
     * 菜单导航,权限信息
     * @param int $user_id
     * @return array
     */
    public function getMenuNav(int $user_id): array
    {
        $redis = redis();
        $app_name = env('APP_NAME');
        $cacheMenuNav = $redis->get($app_name . "_menu_nav:" . $user_id);
        /*if (!empty($cacheMenuNav)) {
            return json_decode($cacheMenuNav, true);
        }*/
        if ($user_id != Constants::SYS_ADMIN_ID) {
            $role_ids = Db::table('sys_user_role')->where("user_id", $user_id)->pluck('role_id');
            $role_ids = $role_ids->toArray();
            $datas = SysRoleMenu::query()->whereIn("role_id", implode(',', $role_ids))->get();
        } else {
            $datas = SysRoleMenu::query()->get();
        }
        $menu_ids = array_column($datas->toArray(), 'menu_id');
        $result = $this->getUserMenusPermissions($menu_ids);
        //$redis->set($app_name . "_menu_nav:" . $user_id, json_encode($result), 60); //暂时设置60秒
        return $result;
    }

    /**
     * 获取菜单和权限
     * @param $menu_ids
     * @return array
     */
    private function getUserMenusPermissions($menu_ids)
    {
        $menu_category = SysMenu::query()->where('parent_id', 0)->where('type', 0)->whereIn('menu_id', $menu_ids)->orderBy('order_num')->get()->toArray();
        $menuList = [];
        foreach ($menu_category as $key => $value) {
            $model = SysMenu::query()->where("menu_id", $value['menu_id'])->first();
            $format = SysMenuFormatter::instance()->base($model);
            $menus = SysMenu::query()->where('parent_id', $model->menu_id)->where('type', 1)->whereIn('menu_id', $menu_ids)->orderBy('order_num')->get()->toArray();
            $arr = [];
            foreach ($menus as $v) {
                $arr [] = SysMenuFormatter::instance()->forArray($v);
            }
            $format['list'] = $arr;
            $menuList[] = $format;
        }

        $permissionArrs = SysMenu::query()->whereIn('menu_id', $menu_ids)->orderBy('order_num')->get()->toArray();
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
     * 获取系统用户信息
     * @param $userId
     * @return mixed
     */
    public function getSysUserData(int $userId)
    {
        try {

            $model = SysUser::query()->where('user_id', $userId)->first();
            $role_ids = Db::table('sys_user_role')->where("user_id", $userId)->pluck('role_id');
            $model->roleIdList = $role_ids;
            $format = SysUserFormatter::instance()->base($model);
            return $format;

        } catch (\Exception $e) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, $e->getMessage());
        }
    }

}