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
use App\Model\Dao\SysUserDao;
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

        $username = (string)$this->request->input('username');
        $password = (string)$this->request->input('password');

        try {

            $sysUser = ApplicationContext::getContainer()->get(SysUserDao::class)->getOne($username);

            $format = SysUserFormatter::instance()->base($sysUser);

            if (!password_verify($password, $format['password'])) {
                return $this->response->error("用户名或密码错误");
            }

            if ($format['status'] != 1) {
                return $this->response->error("该用户禁止登陆");
            }

            $token = JwtInstance::instance()->encode($sysUser);
            return $this->response->success([
                'token' => $token,
                'expire' => 43200
            ]);

        } catch (\Exception $e) {
            return $this->response->error("用户名或密码错误");
        }
    }


    /**
     * 用户信息
     * @SysLogAnnotation()
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
     * @SysLogAnnotation()
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
     * @SysLogAnnotation()
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
     * 保存管理员
     * @SysLogAnnotation()
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
        } else {
            return $this->response->error("保存失败");
        }

    }


    /**
     * update管理员
     * @SysLogAnnotation()
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sysUserUpdate()
    {

        $currentLoginUserId = JwtInstance::instance()->build()->getId();

        $username = (string)$this->request->input('username');
        $password = (string)$this->request->input('password');
        $mobile = $this->request->input('mobile');
        $email = (string)$this->request->input('email');
        $roleIdList = $this->request->input('roleIdList'); //组数
        $salt = (string)$this->request->input('salt');
        $status = (int)$this->request->input('status');
        $userId = (int)$this->request->input('userId');

        $result = $this->sysUserService->sysUserSave($username, $password, $email, $mobile, $roleIdList, $salt, $status, $currentLoginUserId, $userId);

        if ($result == false && $status == 0 && ($currentLoginUserId == $userId)) {
            return $this->response->error("不能禁用当前登录用户");
        }

        if ($result) {
            return $this->response->success();
        } else {
            return $this->response->error("修改失败");
        }
    }


    /**
     * 删除管理员
     * url:sys/user/delete
     * @SysLogAnnotation()
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sysUserDelete()
    {

        $userId = JwtInstance::instance()->build()->getId();

        $params = $this->request->post();

        if (!is_array($params) || empty($params)) {
            return $this->response->error("提交错误");
        }

        if (in_array("1", $params)) {
            return $this->response->error("超级管理员不能删除");
        }

        $result = $this->sysUserService->sysUserDelete($params, $userId);

        if ($result) {
            return $this->response->success();
        } else {
            return $this->response->error("删除失败");
        }
    }


    /**
     * sys/user/password
     * @SysLogAnnotation()
     * 修改密码
     */
    public function password()
    {
        $userId = JwtInstance::instance()->build()->getId();
        $sysUser = JwtInstance::instance()->build()->getSysUser();

        $params = $this->request->post();

        if (!is_array($params) || empty($params)) {
            return $this->response->error("提交错误");
        }

        $format = SysUserFormatter::instance()->base($sysUser);

        if (!password_verify($params['password'], $format['password'])) {
            return $this->response->error("原密码错误");
        }

        $result = $this->sysUserService->sysUserSave($format['username'], trim($params['newPassword']), $format['email'], $format['mobile'], [], "", $format['status'], null, $userId);

        if ($result) {
            return $this->response->success();
        } else {
            return $this->response->error("修改失败");
        }
    }


    /**
     * sys/logout
     */
    public function sysLogout()
    {
        $userId = JwtInstance::instance()->build()->getId();
        return $this->response->success();
    }
}