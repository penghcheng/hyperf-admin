<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/21 0021
 * Time: 10:54
 */

namespace App\Service;


use App\Model\Dao\SysUserDao;
use App\Model\SysMenu;
use App\Service\Formatter\SysMenuFormatter;
use App\Service\Formatter\SysUserFormatter;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;

class SysUserService extends Service
{

    /**
     * @Inject()
     *
     * @var SysUserDao
     */
    protected $sysUserDao;


    /**
     * 菜单导航,权限信息
     * @param int $user_id
     * @return array
     */
    public function getNemuNav(int $user_id):array
    {
        $roleModel = null;
        if($user_id !=1){
            $roleModel = $this->sysUserDao->getUserRole($user_id);
        }
        return $this->getUserMenusPermissions($user_id != 1 ? $roleModel['role_id']: 0,$user_id);
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


    /**
     * 管理员list
     * @param int $user_id
     * @return array
     */
    public function getSysUserList(int $user_id, string $username,int $pageSize = 10,int $currPage = 1):array
    {
        $totalCount = $this->sysUserDao->getTotalCount($user_id,$username);

        if($totalCount>0){
            $totalPage = ceil($totalCount/$pageSize);
        }else{
            $totalPage = 0;
        }

        if($currPage <= 0 || $currPage > $totalPage){
            $currPage = 1;
        }

        $startCount = ($currPage-1)*$pageSize;

        $sysUsers = Db::select("SELECT * FROM sys_user a JOIN (select user_id from sys_user limit ".$startCount.", ".$pageSize.") b ON a.user_id = b.user_id where a.username like '%".$username."%';");

        if(!empty($sysUsers)){
            $sysUsers = SysUserFormatter::instance()->formatArr($sysUsers);
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

}