<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/5
 * Time: 9:05
 */

namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Service\SysMenuService;
use App\Service\SysUserService;
use Hyperf\Di\Annotation\Inject;

class MenuController extends AbstractController
{

    /**
     * @Inject()
     * @var SysMenuService
     */
    private $sysMenuService;

    /**
     * 登录用户的菜单和权限
     */
    public function sysMenuNav()
    {
        $sys_user = $this->request->getAttribute("user");
        [$menuList, $permissions] = $this->sysMenuService->getMenuNav($sys_user['user_id']);
        return $this->response->success([
            'menuList' => $menuList,
            'permissions' => $permissions
        ]);
    }

    /**
     * 获取Menu列表根据用户的权限
     */
    public function sysMenuList()
    {
        $sys_user = $this->request->getAttribute("user");
        $result = $this->sysMenuService->getSysMenuList($sys_user['user_id']);
        return $this->response->json($result);
    }

    public function sysMenuSelect()
    {

    }


    public function sysMenuInfo($id)
    {
    }

    public function sysMenuSave()
    {

    }

    public function sysMenuUpdate()
    {

    }

    public function sysMenuDelete($id)
    {
//        return $this->response->error('测试环境不能删除');
        $sys_user = $this->request->getAttribute("user");

        $result = $this->sysMenuService->getSysMenuDelete($id);
        if ($result === true) {
            return $this->response->success();
        }
        if ($result === false) {
            return $this->response->error('删除失败');
        } else {
            return $this->response->error('存在下级菜单不能直接删除');
        }
    }
}