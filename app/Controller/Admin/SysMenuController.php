<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: penghcheng
 * Date: 2019/10/21 0021
 * Time: 10:04
 */

namespace App\Controller\Admin;


use App\Annotation\SysLogAnnotation;
use App\Controller\AbstractController;
use App\Service\Instance\JwtInstance;
use App\Service\SysUserService;
use Hyperf\Di\Annotation\Inject;

/**
 * @SysLogAnnotation()
 */
class SysMenuController extends AbstractController
{

    /**
     * @Inject
     * @var SysUserService
     */
    protected $sysUserService;


    /**
     * 用户菜单导航
     */
    public function sysMenuNav()
    {

        $userId = JwtInstance::instance()->build()->getId();

        [$menuList, $permissions] = $this->sysUserService->getNemuNav($userId);

        return $this->response->success([
            'menuList' => $menuList,
            'permissions' => $permissions
        ]);
    }


    /**
     * /sys/menu/list
     * 获取Menu列表根据用户的权限
     */
    public function sysMenuList()
    {

        $userId = JwtInstance::instance()->build()->getId();

        $result = $this->sysUserService->getSysNemuList($userId);

        return $this->response->json($result);
    }


    /**
     * /sys/menu/select
     * 选择Menu列表
     */
    public function sysMenuSelect()
    {
        $userId = JwtInstance::instance()->build()->getId();
        $result = $this->sysUserService->getSysNemuSelect();

        return $this->response->success([
            'menuList' => $result
        ]);
    }

    /**
     * 保存Menu
     * sys/menu/save
     */
    public function sysMenuSave()
    {

        $userId = JwtInstance::instance()->build()->getId();

        $parentId = $this->request->input('parentId');
        $type = $this->request->input('type');
        $orderNum = $this->request->input('orderNum');
        $url = (string)$this->request->input('url');
        $perms = (string)$this->request->input('perms');
        $name = (string)$this->request->input('name');
        $icon = (string)$this->request->input('icon');

        $params = [
            'parent_id' => $parentId,
            'name' => $name,
            'url' => $url,
            'perms' => $perms,
            'type' => $type,
            'icon' => $icon,
            'order_num' => $orderNum
        ];

        $result = $this->sysUserService->sysNemuSave($params);

        if ($result) {
            return $this->response->success();
        } else {
            return $this->response->error('保存失败');
        }
    }


    /**
     * 根据id获取menu
     * @param $id
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sysMenuInfo($id)
    {
        $userId = JwtInstance::instance()->build()->getId();

        $result = $this->sysUserService->getSysMenuInfo($id);

        if ($result) {
            return $this->response->success(['menu' => $result]);
        } else {
            return $this->response->error('获取失败');
        }
    }

    /**
     * 更新菜单
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sysMenuUpdate()
    {
        return $this->response->error('测试环境不能修改');
        $userId = JwtInstance::instance()->build()->getId();

        $menuId = (int)$this->request->input('menuId');
        $parentId = (int)$this->request->input('parentId');
        $type = (int)$this->request->input('type');
        $orderNum = (int)$this->request->input('orderNum');
        $url = (string)$this->request->input('url');
        $perms = (string)$this->request->input('perms');
        $name = (string)$this->request->input('name');
        $icon = (string)$this->request->input('icon');

        $params = [
            'menu_id' => $menuId,
            'parent_id' => $parentId,
            'name' => $name,
            'url' => $url,
            'perms' => $perms,
            'type' => $type,
            'icon' => $icon,
            'order_num' => $orderNum
        ];

        $result = $this->sysUserService->sysNemuUpdate($params);

        if ($result) {
            return $this->response->success();
        } else {
            return $this->response->error('更新失败');
        }
    }

    /**
     * 删除菜单
     * @param $id
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sysMenuDelete($id)
    {
        return $this->response->error('测试环境不能删除');
        $userId = JwtInstance::instance()->build()->getId();

        $result = $this->sysUserService->getSysMenuDelete($id);
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