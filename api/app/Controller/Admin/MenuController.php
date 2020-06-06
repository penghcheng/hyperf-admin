<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/5
 * Time: 9:05
 */

namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Service\SysUserService;
use Hyperf\Di\Annotation\Inject;

class MenuController extends AbstractController
{

    /**
     * @Inject()
     * @var SysUserService
     */
    private $sysUserService;

    /**
     * 登录用户的菜单和权限
     */
    public function sysNenuNav()
    {
        $sys_user = $this->request->getAttribute("user");
        [$menuList, $permissions] = $this->sysUserService->getNemuNav($sys_user['user_id']);
        return $this->response->success([
            'menuList' => $menuList,
            'permissions' => $permissions
        ]);
    }
}