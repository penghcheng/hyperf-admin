<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/5
 * Time: 9:06
 */

namespace App\Controller\Admin;


use App\Constants\ErrorCode;
use App\Controller\AbstractController;
use App\Exception\BusinessException;
use App\Service\SysUserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Phper666\JWTAuth\JWT;

class UserController extends AbstractController
{
    /**
     * @Inject()
     * @var SysUserService
     */
    private $sysUserService;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    /**
     * @Inject
     *
     * @var JWT
     */
    protected $jwt;

    /**
     * 管理员登录
     */
    public function login()
    {
        $input = $this->request->all();
        $validator = $this->validationFactory->make(
            $input,
            [
                'username' => 'required',
                'password' => 'required',
            ],
            [
                'username.required' => 'username is required',
                'password.required' => 'password is required',
            ]
        );

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(ErrorCode::SERVER_ERROR, $errorMessage);
        }

        $user = $this->sysUserService->login($input['username'], $input['password']);
        //stdLog()->error(json_encode($user));
        $token = $this->jwt->getToken($user);
        $data = [
            'token' => (string)$token,
            'expire' => $this->jwt->getTTL()
        ];
        return $this->response->success($data);
    }

    /**
     * 用户信息
     */
    public function getInfoByLoginUserId()
    {
        $sys_user = $this->request->getAttribute("user");
        $select = [
            'user_id as userId',
            'username',
            'password',
            'salt',
            'email',
            'mobile',
            'status',
            'create_user_id as createUserId',
            'create_time as createTime'
        ];
        $data = $this->sysUserService->findForSelect($sys_user['user_id'], $select);
        return $this->response->success([
            'user' => $data
        ]);
    }

    /**
     * 管理员用户列表
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sysUserList()
    {
        $sys_user = $this->request->getAttribute("user");
        $username = (string)$this->request->input('username');
        $page = (int)$this->request->input('page');
        $limit = (int)$this->request->input('limit');

        $result = $this->sysUserService->getSysUserList($sys_user['user_id'], $username, $limit, $page);

        return $this->response->success([
            'page' => $result
        ]);
    }

    public function getInfoByUserId()
    {

    }

    /**
     * 退出
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function sysLogout()
    {
//        $this->jwt->logout();
        return $this->response->success();
    }
}