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

        var_dump("username:" . $username);

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
}