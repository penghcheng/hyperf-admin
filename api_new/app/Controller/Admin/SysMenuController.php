<?php
/**
 * Created by PhpStorm.
 * User: derek
 * Date: 2019/12/19
 * Time: 22:11
 */

namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Kernel\Util\JwtInstance;
use App\Service\SysService;
use Hyperf\Di\Annotation\Inject;

class SysMenuController extends AbstractController
{
    /**
     * @Inject()
     * @var SysService
     */
    private $sysService;

    /**
     * 用户菜单导航
     */
    public function sysMenuNav()
    {
        $userId = JwtInstance::instance()->build()->getId();

        [$menuList, $permissions] = $this->sysService->getMenuNav($userId);

        return $this->response->success([
            'menuList' => $menuList,
            'permissions' => $permissions
        ]);
    }
}