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
use App\Request\LoginRequest;
use App\Service\SysService;
use Hyperf\Di\Annotation\Inject;

class SysUserController extends AbstractController
{
    /**
     * @Inject()
     * @var SysService
     */
    private $sysService;

    /**
     * 登录
     * @param LoginRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function login(LoginRequest $request)
    {
        $username = (string)$request->input('username');
        $password = (string)$request->input('password');

        try {

            $sysUser = $this->sysService->login($username, $password);
            $token = JwtInstance::instance()->encode($sysUser);
            return $this->response->success([
                'token' => $token,
                'expire' => config("sys_token_exp"),
            ]);

        } catch (\Exception $e) {
            return $this->response->error($e->getMessage());
        }
    }

    /**
     * 用户信息
     */
    public function getInfoByLoginUserId()
    {

        $userId = JwtInstance::instance()->build()->getId();
        $model = $this->sysService->getSysUserData($userId);

        return $this->response->success([
            'user' => $model
        ]);
    }
}