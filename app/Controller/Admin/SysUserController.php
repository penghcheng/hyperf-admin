<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/21 0021
 * Time: 10:04
 */

namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Model\Dao\SysUserDao;
use App\Model\SysUser;
use App\Service\Formatter\SysUserFormatter;
use App\Service\Instance\JwtInstance;
use App\Service\SysUserService;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Di\Annotation\Inject;


class SysUserController extends AbstractController
{

    /**
     * @Inject
     * @var SysUserService
     */
    protected $sysUserService;

    /**
     * 管理员登录
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function login()
    {
        //captcha: "87wxy"
        //password: "admin"
        //t: 1571621141329
        //username: "admin"
        //uuid: "10b0c6c3-21df-498c-8daf-57320990461b"

        $username = (string)$this->request->input('username');
        $password = (string)$this->request->input('password');

        $sysUser = ApplicationContext::getContainer()->get(SysUserDao::class)->getOne($username);

        $token = JwtInstance::instance()->encode($sysUser);

        return $this->response->success([
            'token' => $token,
            'expire' => 43200
        ]);

    }

    /**
     * 用户菜单导航
     */
    public function menu_nav()
    {

        $userId = JwtInstance::instance()->build()->getId();

        [$menuList, $permissions] = $this->sysUserService->getNemuNav($userId);

        return $this->response->success([
            'menuList' => $menuList,
            'permissions' => $permissions
        ]);
    }


    /**
     * 用户信息
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function info()
    {
        $userId = JwtInstance::instance()->build()->getId();
        $model = SysUser::query()->where('user_id', $userId)->first();
        $format = SysUserFormatter::instance()->base($model);

        return $this->response->success([
            'user' => $format
        ]);
    }


    /**
     * 管理员list
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sysUserList()
    {
        $userId = JwtInstance::instance()->build()->getId();

        $username = (string)$this->request->input('username');
        $page = (int)$this->request->input('page');
        $limit = (int)$this->request->input('limit');

        $result = $this->sysUserService->getSysUserList($userId,$username,$limit,$page);

        return $this->response->success([
            'page' => $result
        ]);
    }

    /**
     * 角色管理list
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sysRoleList()
    {
        $userId = JwtInstance::instance()->build()->getId();

        $roleName = (string)$this->request->input('roleName');
        $page = (int)$this->request->input('page');
        $limit = (int)$this->request->input('limit');

        $result = $this->sysUserService->getSysRoleList($userId,$roleName,$limit,$page);

        return $this->response->success([
            'page' => $result
        ]);
    }


}