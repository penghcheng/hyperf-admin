<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: penghcheng
 * Date: 2019/10/21 0021
 * Time: 10:04
 */

namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Service\Instance\JwtInstance;
use App\Service\SysUserService;
use Hyperf\Di\Annotation\Inject;


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

}