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

        $user = $this->sysUserService->login($input['username'],$input['password']);
        $token = $this->jwt->getToken($user);
        $data  = [
            'token' => (string) $token,
            'expire'   => $this->jwt->getTTL()
        ];
        return $this->response->success($data);
    }
}