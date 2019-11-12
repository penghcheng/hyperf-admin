<?php
/**
 * Created by PhpStorm.
 * User: penghcheng
 * Date: 2019/10/21 0021
 * Time: 10:54
 */

namespace App\Service;


use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Model\Dao\SysConfigDao;
use App\Model\Dao\SysLogDao;
use App\Model\Dao\SysRoleDao;
use App\Model\Dao\SysUserDao;
use App\Model\SysMenu;
use App\Model\SysUser;
use App\Service\Formatter\SysConfigFormatter;
use App\Service\Formatter\SysLogFormatter;
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
     * @Inject()
     * @var SysLogDao
     */
    protected $sysLogDao;

    /**
     * @Inject()
     * @var SysConfigDao
     */
    private $sysConfigDao;

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
        $cacheMenuNav = $redis->get($app_name . "_menu_nav:" . $user_id);

        /*if (!empty($cacheMenuNav)) {
            return json_decode($cacheMenuNav, true);
        }*/

        if ($user_id != 1) {

            $role_ids = Db::table('sys_user_role')->where("user_id", $user_id)->pluck('role_id');
            $role_ids = $role_ids->toArray();
            $datas = Db::select("SELECT * FROM sys_role_menu where role_id in (" . implode(',', $role_ids) . ");");

        } else {
            $datas = Db::select('SELECT * FROM sys_menu;');
        }
        $menu_ids = array_column($datas, 'menu_id');

        $result = $this->getUserMenusPermissions($menu_ids);

        $redis->set($app_name . "_menu_nav:" . $user_id, json_encode($result), 60); //暂时设置60秒
        return $result;
    }


    /**
     * 获取菜单和权限
     * @param $menu_ids
     * @return array
     */
    private function getUserMenusPermissions($menu_ids)
    {
        $menu_category = Db::select('SELECT * FROM sys_menu where  parent_id = 0 and type = 0 and menu_id in (' . implode(',', $menu_ids) . ') order by order_num asc;');

        $menuList = [];
        foreach ($menu_category as $key => $value) {
            $model = SysMenu::query()->where("menu_id", $value['menu_id'])->first();

            $format = SysMenuFormatter::instance()->base($model);

            $menus = Db::select('SELECT * FROM sys_menu where  parent_id = ' . $format['menuId'] . ' and menu_id in (' . implode(',', $menu_ids) . ') and type = 1 order by order_num asc;');

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
     * @param int $user_id
     * @return array
     */
    public function getSysNemuList(int $user_id): array
    {

        if ($user_id != 1) {
            $role_ids = Db::table('sys_user_role')->where("user_id", $user_id)->pluck('role_id');
            $role_ids = $role_ids->toArray();
            $datas = Db::select("SELECT * FROM sys_role_menu where role_id in (" . implode(',', $role_ids) . ");");
        } else {
            $datas = Db::select('SELECT * FROM sys_menu;');
        }

        if (empty($datas)) {
            return [];
        }

        $menu_ids = array_column($datas, 'menu_id');
        $menu_ids = array_unique($menu_ids);

        $sys_menus = Db::select("SELECT s1.*,s2.name as parentName FROM sys_menu s1 LEFT JOIN sys_menu s2 ON s1.parent_id = s2.menu_id where s1.menu_id in (" . implode(',', $menu_ids) . ") order by s1.order_num ASC ;");
        $sys_menus = SysMenuFormatter::instance()->arrayFormat($sys_menus);

        return $sys_menus;
    }


    /**
     * 获取顶级和一级的菜单
     * @return array
     */
    public function getSysNemuSelect()
    {
        $datas = Db::select('SELECT * FROM sys_menu where parent_id in (0,1) order by order_num ASC ;');

        if (empty($datas)) {
            return [];
        }
        $menu_ids = array_column($datas, 'menu_id');
        $menu_ids = array_unique($menu_ids);

        $sys_menus = Db::select("SELECT s1.*,s2.name as parentName FROM sys_menu s1 LEFT JOIN sys_menu s2 ON s1.parent_id = s2.menu_id where s1.menu_id in (" . implode(',', $menu_ids) . ") order by s1.order_num ASC ;");
        $sys_menus = SysMenuFormatter::instance()->arrayFormat($sys_menus);

        $topMenu = [
            "menuId" => 0,
            "parentId" => -1,
            "parentName" => null,
            "name" => "一级菜单",
            "url" => null,
            "perms" => null,
            "type" => null,
            "icon" => null,
            "orderNum" => null,
            "open" => true,
            "list" => null
        ];

        $sys_menus [] = $topMenu;

        return $sys_menus;
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

        $sysUsers = Db::select("SELECT * FROM sys_user a where " . $where . " order by a.user_id desc limit " . $startCount . "," . $pageSize);

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
     * @param string $roleName
     * @param int $pageSize
     * @param int $currPage
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

        $sysRoles = Db::select("SELECT * FROM sys_role a where " . $where . " order by a.role_id desc limit " . $startCount . "," . $pageSize);

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

            if ($createUserId == $updateUserId && $status == 0) {
                return false;
            }

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

            $roles = [];
            if (!empty($roleIdList) && !empty($updateUserId)) {

                Db::table('sys_user_role')->where("user_id", $updateUserId)->delete();

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
    }


    /**
     * 保存角色
     * @param int $userId
     * @param string $roleName
     * @param $remark
     * @param array $menuIdList
     * @param string $flag
     * @param int $roleId
     * @return bool|null
     */
    public function sysRoleSave(int $userId, string $roleName, $remark, array $menuIdList, string $flag = 'add', $roleId = 0): ?bool
    {
        if ($userId != 1) {
            $role_ids = Db::table('sys_user_role')->where("user_id", $userId)->pluck('role_id');
            $role_ids = $role_ids->toArray();
            $datas = Db::select("SELECT * FROM sys_role_menu where role_id in (" . implode(',', $role_ids) . ");");
        } else {
            $datas = Db::select('SELECT * FROM sys_menu;');
        }
        $menu_ids = array_column($datas, 'menu_id');
        $menu_ids = array_unique($menu_ids);

        $menu_diffs = array_diff($menu_ids, $menuIdList);
        $menu_diffs = array_values($menu_diffs); //重建数组下标0开始

        // 保存的权限大于当前用户的权限就抛出异常
        if (!empty($menu_diffs) && in_array($menu_diffs[0], $menuIdList)) {
            throw new BusinessException(ErrorCode::USER_INVALID);
        }

        if ($flag == 'add') { // 新增角色
            Db::beginTransaction();
            try {
                $id = Db::table('sys_role')->insertGetId(
                    ['role_name' => $roleName, 'remark' => $remark, 'create_user_id' => $userId, 'create_time' => date("Y-m-d h:i:s")]
                );
                $role_menus = [];
                foreach ($menuIdList as $value) {
                    $role_menus[] = ['role_id' => $id, 'menu_id' => $value];
                }
                Db::table('sys_role_menu')->insert($role_menus);
                Db::commit();
                return true;

            } catch (\Throwable $ex) {
                Db::rollBack();
                return false;
            }

        } else { // 更新角色

            Db::beginTransaction();
            try {

                Db::table('sys_role')->where('role_id', $roleId)->update(['role_name' => $roleName, 'remark' => $remark]);

                if ((!empty($menu_diffs) && !in_array($menu_diffs[0], $menu_ids))) {
                    throw new BusinessException(ErrorCode::USER_INVALID);
                }

                // 获取当前角色的menu_id
                $currentMenuIds = Db::table('sys_role_menu')->where("role_id", $roleId)->pluck('menu_id');
                $currentMenuIds = $currentMenuIds->toArray();

                // 对比当前和提交的菜单的差集
                if (json_encode($currentMenuIds, true) == json_encode($menuIdList, true)) {
                    Db::commit();
                    return true;
                }

                Db::table('sys_role_menu')->where('role_id', $roleId)->delete();
                $role_menus = [];
                foreach ($menuIdList as $value) {
                    $role_menus[] = ['role_id' => $roleId, 'menu_id' => $value];
                }

                Db::table('sys_role_menu')->insert($role_menus);
                Db::commit();
                return true;

            } catch (\Throwable $ex) {
                Db::rollBack();
                return false;
            }
        }
    }


    /**
     * 获取角色信息
     * @param $role_id
     * @return array
     */
    public function getSysRoleInfo($role_id): array
    {

        try {

            $datas = Db::select("SELECT * FROM sys_role_menu where role_id = " . $role_id . ";");
            $menu_ids = array_column($datas, 'menu_id');
            $menu_ids = array_unique($menu_ids);

            $model = $this->sysRoleDao->first($role_id);
            $model->menuIdList = $menu_ids;
            return SysRoleFormatter::instance()->base($model);

        } catch (\Exception $e) {
            return [];
        }
    }


    /**
     * 管理员删除
     * @param array $params
     * @param $userId
     * @return bool|null
     */
    public function sysUserDelete(array $params, $userId): ?bool
    {
        Db::beginTransaction();
        try {
            if ($userId == 1) {

                Db::table('sys_user')->whereIn("user_id", $params)->delete();
                Db::table('sys_user_role')->whereIn("user_id", $params)->delete();

            } else {

                $user_ids = Db::table('sys_user')->whereIn("user_id", $params)->where("create_user_id", $userId)->pluck("user_id");

                Db::table('sys_user')->whereIn("user_id", $user_ids)->where("create_user_id", $userId)->delete();
                Db::table('sys_user_role')->whereIn("user_id", $user_ids)->delete();
            }


            Db::commit();
            return true;

        } catch (\Throwable $ex) {
            Db::rollBack();
            return false;
        }

    }

    /**
     * 删除角色
     * @param array $params
     * @param $userId
     * @return bool
     */
    public function sysRoleDelete(array $params, $userId)
    {

        Db::beginTransaction();
        try {
            if ($userId == 1) {

                Db::table('sys_role')->whereIn("role_id", $params)->delete();
                Db::table('sys_role_menu')->whereIn("role_id", $params)->delete();

            } else {
                $role_ids = Db::table('sys_role')->whereIn("role_id", $params)->where("create_user_id", $userId)->pluck("role_id");

                Db::table('sys_role')->whereIn("role_id", $role_ids)->where("create_user_id", $userId)->delete();
                Db::table('sys_role_menu')->whereIn("role_id", $role_ids)->delete();
            }

            Db::commit();
            return true;

        } catch (\Throwable $ex) {
            Db::rollBack();
            return false;
        }
    }

    /**
     * 保存菜单
     * @param array $params
     * @return int
     */
    public function sysNemuSave(array $params)
    {
        return Db::table('sys_menu')->insertGetId($params);
    }


    /**
     * 获取条菜单记录
     * @param $id
     * @return array|null
     */
    public function getSysMenuInfo($id): ?array
    {
        $model = Db::table('sys_menu')->where('menu_id', $id)->first();
        return !empty($model) ? SysMenuFormatter::instance()->forArray($model) : null;
    }


    /**
     * 更新菜单
     * @param array $params
     * @return int
     */
    public function sysNemuUpdate(array $params)
    {
        return Db::table('sys_menu')->where("menu_id", $params['menu_id'])->update($params);
    }

    /**
     * 删除菜单
     * @param $id
     * @return int
     */
    public function getSysMenuDelete($id)
    {
        $hasParent = Db::table('sys_menu')->where('parent_id', $id)->first();
        if (!empty($hasParent)) {
            return -1;
        }
        Db::beginTransaction();
        try {
            Db::table('sys_menu')->where('menu_id', $id)->delete();
            Db::table('sys_role_menu')->where('menu_id', $id)->delete();

            Db::commit();
            return true;

        } catch (\Throwable $ex) {
            Db::rollBack();
            return false;
        }
    }


    /**
     * 返回日志管理列表
     * @param string $key
     * @param int $pageSize
     * @param int $currPage
     * @return array
     */
    public function getSysLogList(string $key, int $pageSize = 10, int $currPage = 1): array
    {
        $totalCount = $this->sysLogDao->getTotalCount($key);

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

        if (!empty($key)) {
            $where .= " and a.username like '%" . $key . "%' or  a.operation like '%" . $key . "%'";
        }

        $sysLogs = Db::select("SELECT * FROM sys_log a where " . $where . " order by a.id desc limit " . $startCount . "," . $pageSize);


        if (!empty($sysLogs)) {
            $sysLogs = SysLogFormatter::instance()->arrayFormat($sysLogs);
        }

        $result = [
            'totalCount' => $totalCount,
            'pageSize' => $pageSize,
            'totalPage' => $totalPage,
            'currPage' => $currPage,
            'list' => $sysLogs
        ];
        return $result;
    }

    /**
     * 保存日志
     * @param array $params
     * @return bool
     */
    public function sysLogSave(array $params)
    {
        return Db::table("sys_log")->insert($params);
    }

    /**
     * 返回参数管理列表
     * @param string $paramKey
     * @param int $pageSize
     * @param int $currPage
     * @return array
     */
    public function getSysConfigList(string $paramKey, int $pageSize = 10, int $currPage = 1): array
    {
        $totalCount = $this->sysConfigDao->getTotalCount($paramKey);

        if ($totalCount > 0) {
            $totalPage = ceil($totalCount / $pageSize);
        } else {
            $totalPage = 0;
        }

        if ($currPage <= 0 || $currPage > $totalPage) {
            $currPage = 1;
        }

        $startCount = ($currPage - 1) * $pageSize;

        $where = " 1=1 and a.status = 1 ";

        if (!empty($paramKey)) {
            $where .= " and a.param_key like '%" . $paramKey . "%' or a.remark like '%" . $paramKey . "%'";
        }

        $sysConfigs = Db::select("SELECT * FROM sys_config a where " . $where . " order by a.id desc limit " . $startCount . "," . $pageSize);

        if (!empty($sysConfigs)) {
            $sysConfigs = SysConfigFormatter::instance()->arrayFormat($sysConfigs);
        }

        $result = [
            'totalCount' => $totalCount,
            'pageSize' => $pageSize,
            'totalPage' => $totalPage,
            'currPage' => $currPage,
            'list' => $sysConfigs
        ];
        return $result;
    }

    /**
     * 保存参数
     * @param string $paramKey
     * @param string $paramValue
     * @param string $remark
     * @param int $id
     * @return bool|string
     */
    public function getSysConfigSave(string $paramKey, string $paramValue, string $remark, int $id)
    {
        $data = [
            'param_key' => $paramKey,
            'param_value' => $paramValue,
            'remark' => $remark
        ];
        if (!empty($id)) {
            $data['id'] = $id;
        }
        try {
            $this->sysConfigDao->firstOrNew($data);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 获取参数
     * @param $id
     * @return string
     */
    public function getSysConfigInfo($id)
    {
        try {
            $model = $this->sysConfigDao->first($id);
            return SysConfigFormatter::instance()->base($model);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 删除参数
     */
    public function sysConfigDelete(array $params)
    {
        return Db::table('sys_config')->whereIn("id", $params)->delete();
    }

}