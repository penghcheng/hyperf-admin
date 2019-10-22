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
use Hyperf\DbConnection\Db;
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
     */
    public function getInfoByLoginUserId()
    {

        $userId = JwtInstance::instance()->build()->getId();

        $model = $this->sysUserService->getSysUserData($userId);

        $format = SysUserFormatter::instance()->base($model);

        return $this->response->success([
            'user' => $format
        ]);
    }

    /**
     * 用户信息 userId
     * @param $id
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getInfoByUserId($id)
    {
        JwtInstance::instance()->build()->getId();

        $userId = $id;

        $model = $this->sysUserService->getSysUserData($userId);

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

        $result = $this->sysUserService->getSysUserList($userId, $username, $limit, $page);

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

        $result = $this->sysUserService->getSysRoleList($userId, $roleName, $limit, $page);

        return $this->response->success([
            'page' => $result
        ]);
    }

    /**
     * select角色list
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sysRoleSelect()
    {
        $userId = JwtInstance::instance()->build()->getId();

        $result = $this->sysUserService->getSysRoleList($userId, "", 999, 1);

        return $this->response->success([
            'list' => $result['list']
        ]);
    }

    /**
     * 保存管理员
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function sysUserSave()
    {

        $createUserId = JwtInstance::instance()->build()->getId();

        $username = (string)$this->request->input('username');
        $password = (string)$this->request->input('password');
        $mobile = $this->request->input('mobile');
        $email = (string)$this->request->input('email');
        $roleIdList = $this->request->input('roleIdList'); //组数
        $salt = (string)$this->request->input('salt');
        $status = (int)$this->request->input('status');

        $result = $this->sysUserService->sysUserSave($username, $password, $email, $mobile, $roleIdList, $salt, $status, $createUserId);

        if ($result) {
            return $this->response->success();
        }else{
            return $this->response->error("保存失败");
        }

    }


    /**
     * update管理员
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sysUserUpdate()
    {

        JwtInstance::instance()->build()->getId();

        $username = (string)$this->request->input('username');
        $password = (string)$this->request->input('password');
        $mobile = $this->request->input('mobile');
        $email = (string)$this->request->input('email');
        $roleIdList = $this->request->input('roleIdList'); //组数
        $salt = (string)$this->request->input('salt');
        $status = (int)$this->request->input('status');
        $userId = (int)$this->request->input('userId');

        $result = $this->sysUserService->sysUserSave($username, $password, $email, $mobile, $roleIdList, $salt, $status, null, $userId);
        if ($result) {
            return $this->response->success();
        }else{
            return $this->response->error("修改失败");
        }
    }


}