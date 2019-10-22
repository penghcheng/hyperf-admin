<?php
/**
 * Created by PhpStorm.
 * User: penghcheng
 * Date: 2019/10/21 0021
 * Time: 10:54
 */

namespace App\Service;


use App\Model\Dao\SysRoleDao;
use App\Model\Dao\SysUserDao;
use App\Model\SysMenu;
use App\Model\SysUser;
use App\Service\Formatter\SysMenuFormatter;
use App\Service\Formatter\SysRoleFormatter;
use App\Service\Formatter\SysUserFormatter;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\ApplicationContext;

class SysUserService extends Service
{

    /**
     * @Inject()
     *
     * @var SysUserDao
     */
    protected $sysUserDao;


    /**
     * @Inject()
     * @var SysRoleDao
     */
    protected $sysRoleDao;


    /**
     * 获取系统用户信息
     * @param $userId
     * @return mixed
     */
    public function getSysUserData($userId)
    {
        $model = SysUser::query()->where('user_id', $userId)->first();

        $role_ids = Db::table('sys_user_role')->where("user_id", $userId)->pluck('role_id');
        $model->roleIdList = $role_ids;

        return $model;
    }


    /**
     * 菜单导航,权限信息
     * @param int $user_id
     * @return array
     */
    public function getNemuNav(int $user_id): array
    {
        $container = ApplicationContext::getContainer();
        $redis = $container->get(\Redis::class);

        $app_name = env('APP_NAME');
        $cache_memunv = $redis->get($app_name . "_menu_nav:" . $user_id);

        if (!empty($cache_memunv)) {
            return json_decode($cache_memunv, true);
        }
        if ($user_id != 1) {
            $role_ids = Db::table('sys_user_role')->where("user_id", $user_id)->pluck('role_id');
            $role_ids = $role_ids->toArray();
            $datas = Db::select("SELECT * FROM sys_role_menu where role_id in (" . implode(',', $role_ids) . ");");
        } else {
            $datas = Db::select('SELECT * FROM sys_menu;');
        }
        $menu_ids = array_column($datas, 'menu_id');
        $result = $this->getUserMenusPermissions($menu_ids, $user_id);

        $redis->set($app_name . "_menu_nav:" . $user_id, json_encode($result), 60 * 60);
        return $result;
    }


    /**
     * 获取菜单和权限
     * @param $menu_ids
     * @return array
     */
    public function getUserMenusPermissions($menu_ids)
    {
        $menu_category = Db::select('SELECT * FROM sys_menu where  parent_id = 0 and type = 0 and menu_id in (' . implode(',', $menu_ids) . ') order by order_num asc;');

        $menuList = [];
        foreach ($menu_category as $key => $value) {
            $model = SysMenu::query()->where("menu_id", $value['menu_id'])->first();
            $format = SysMenuFormatter::instance()->base($model);

            $menus = Db::select('SELECT * FROM sys_menu where  parent_id = ' . $format['menuId'] . ' and type = 1 order by order_num asc;');

            $arr = [];
            foreach ($menus as $v) {
                $arr [] = SysMenuFormatter::instance()->forArray($v);
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


    /**
     * 管理员管理list
     * @param int $user_id
     * @return array
     */
    public function getSysUserList(int $user_id, string $username, int $pageSize = 10, int $currPage = 1): array
    {
        $totalCount = $this->sysUserDao->getTotalCount($user_id, $username);

        if ($totalCount > 0) {
            $totalPage = ceil($totalCount / $pageSize);
        } else {
            $totalPage = 0;
        }

        if ($currPage <= 0 || $currPage > $totalPage) {
            $currPage = 1;
        }

        $startCount = ($currPage - 1) * $pageSize;

        $where = " 1=1 ";
        if ($user_id != 1) {
            $where .= " and a.create_user_id = " . $user_id;
        }

        if (!empty($username)) {
            $where .= " and a.username like '%" . $username . "%'";
        }

        $sysUsers = Db::select("SELECT * FROM sys_user a JOIN (select user_id from sys_user limit " . $startCount . ", " . $pageSize . ") b ON a.user_id = b.user_id where " . $where . ";");

        if (!empty($sysUsers)) {
            $sysUsers = SysUserFormatter::instance()->arrayFormat($sysUsers);
        }

        $result = [
            'totalCount' => $totalCount,
            'pageSize' => $pageSize,
            'totalPage' => $totalPage,
            'currPage' => $currPage,
            'list' => $sysUsers
        ];
        return $result;
    }


    /**
     * 角色管理list
     * @param int $user_id
     * @return array
     */
    public function getSysRoleList(int $user_id, string $roleName, int $pageSize = 10, int $currPage = 1): array
    {
        $totalCount = $this->sysRoleDao->getTotalCount($user_id, $roleName);

        if ($totalCount > 0) {
            $totalPage = ceil($totalCount / $pageSize);
        } else {
            $totalPage = 0;
        }

        if ($currPage <= 0 || $currPage > $totalPage) {
            $currPage = 1;
        }

        $startCount = ($currPage - 1) * $pageSize;

        $where = " 1=1 ";
        if ($user_id != 1) {
            $where .= " and a.create_user_id = " . $user_id;
        }

        if (!empty($roleName)) {
            $where .= " and a.role_name like '%" . $roleName . "%'";
        }

        $sysRoles = Db::select("SELECT * FROM sys_role a JOIN (select role_id from sys_role limit " . $startCount . ", " . $pageSize . ") b ON a.role_id = b.role_id where " . $where . ";");

        if (!empty($sysRoles)) {
            $sysRoles = SysRoleFormatter::instance()->arrayFormat($sysRoles);
        }

        $result = [
            'totalCount' => $totalCount,
            'pageSize' => $pageSize,
            'totalPage' => $totalPage,
            'currPage' => $currPage,
            'list' => $sysRoles
        ];
        return $result;
    }


    /**
     * 保存用户信息
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $mobile
     * @param array $roleIdList
     * @param string $salt
     * @param int $status
     * @param int $createUserId
     * @param int $updateUserId
     * @return bool
     */
    public function sysUserSave(string $username, string $password, string $email, string $mobile, array $roleIdList, string $salt, int $status, ?int $createUserId, int $updateUserId = 0): bool
    {

        if ($updateUserId == 0) {

            // 添加管理员

            $user_id = Db::table('sys_user')->insertGetId([
                'username' => $username,
                'password' => password_hash($password, PASSWORD_BCRYPT, ["cost" => 12]),
                'email' => $email,
                'mobile' => $mobile,
                'salt' => $salt,
                'status' => $status,
                'create_user_id' => $createUserId,
                'create_time' => date("Y-m-d h:i:s")
            ]);

            $roles = [];
            if (!empty($roleIdList) && !empty($user_id)) {
                foreach ($roleIdList as $value) {
                    $roles[] = [
                        'user_id' => $user_id,
                        'role_id' => $value
                    ];
                }
            }

            if (!empty($roles)) {
                Db::table('sys_user_role')->insert($roles);
            }

            return !empty($user_id) ? true : false;

        } else {

            // 更新管理员

            $update = [
                'username' => $username,
                'email' => $email,
                'mobile' => $mobile,
                'salt' => $salt,
                'status' => $status
            ];

            if (!empty($password)) {
                $update['password'] = password_hash($password, PASSWORD_BCRYPT, ["cost" => 12]);
            }

            Db::table('sys_user')->where("user_id", $updateUserId)->update($update);
            Db::table('sys_user_role')->where("user_id", $updateUserId)->delete();

            $roles = [];
            if (!empty($roleIdList) && !empty($updateUserId)) {
                foreach ($roleIdList as $value) {
                    $roles[] = [
                        'user_id' => $updateUserId,
                        'role_id' => $value
                    ];
                }
            }

            if (!empty($roles)) {
                Db::table('sys_user_role')->insert($roles);
            }

            return true;
        }

        return false;
    }

}