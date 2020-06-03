<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/1
 * Time: 9:29
 */

namespace App\Controller\Wechat;


use App\Common\Dao\StudentUserDao;
use App\Common\Helper\SmsHelper;
use App\Constants\ErrorCode;
use App\Controller\AbstractController;
use App\Exception\BusinessException;
use App\Service\SmsService;
use App\Service\UserService;
use EasySwoole\VerifyCode\VerifyCode;
use Hyperf\Apidog\Annotation\ApiController;
use Hyperf\Apidog\Annotation\ApiResponse;
use Hyperf\Apidog\Annotation\FormData;
use Hyperf\Apidog\Annotation\PostApi;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Phper666\JWTAuth\JWT;

/**
 * @ApiController(tag="公众号登录",prefix="we/user",description="")
 */
class UserController extends AbstractController
{

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    /**
     * @Inject()
     * @var SmsService
     */
    protected $smsService;

    /**
     * @Inject()
     * @var StudentUserDao
     */
    protected $studentUserDao;

    /**
     * @Inject()
     * @var UserService
     */
    private $userService;

    /**
     * @Inject
     * @var JWT
     */
    protected $jwt;

    /**
     * @PostApi(path="register", description="注册学生账号")
     * @FormData(key="local_province_id|所在省份id", rule="required")
     * @FormData(key="local_city_id|所在市id", rule="required")
     * @FormData(key="local_district_id|所在区id", rule="required")
     * @FormData(key="user_name|登录账号", rule="required")
     * @FormData(key="password|密码", rule="required")
     * @FormData(key="phone|手机号码", rule="required")
     * @FormData(key="captcha|验证码", rule="required")
     * @FormData(key="school_id|school_id", rule="required")
     */
    public function register()
    {
        $params = $this->request->all();
        $validator = $this->validationFactory->make($params,[
            'user_name'         =>  'required',
            'password'          =>  'required|min:8',
            'local_province_id' =>  'required',
            'local_city_id'     =>  'required',
            'local_district_id' =>  'required',
            'phone'             =>  'required',
            'captcha'           =>  'required',
            'school_id'         =>  'required',
        ],[
            'user_name.required'            =>  '请填写登录账号',
            'local_province_id.required'    =>  '请选择所在省份',
            'local_city_id.required'        =>  '请选择所在市',
            'local_district_id.required'    =>  '请选择所在区',
            'password.min'                  =>  '密码长度最少8位',
            'phone.required'                =>  '填写手机号',
            'captcha.required'              =>  '请输入验证码',
            'school_id.required'            =>  '请选择学校',
        ]);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(ErrorCode::COMMON_ERROR, $errorMessage);
        }
        $this->smsService->verify($params['phone'], 'register', $params['captcha']);  //校验验证码

        $result = $this->studentUserDao->register($params);
        if ($result){
            $user = $this->studentUserDao->login($params['user_name'],$params['password']);
            $token = $this->jwt->getToken($user);
            $data  = [
                'token' => (string) $token,
                'exp'   => $this->jwt->getTTL(),
                'user'  => $user
            ];
            return $this->response->success($data);
        }else{
            throw new BusinessException(ErrorCode::COMMON_ERROR,'注册失败,请重试');
        }
    }

    /**
     * @PostApi(path="login", description="添加一个用户")
     * @FormData(key="username|用户名或者手机号", rule="required")
     * @FormData(key="password|密码", rule="required")
     * @ApiResponse(code="-1", description="参数错误")
     * @ApiResponse(code="0", description="成功", schema={"id":1})
     */
    public function login()
    {
        $validator = $this->validationFactory->make(
            $this->request->all(),
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
            // Handle exception
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(ErrorCode::COMMON_ERROR, $errorMessage);
        }

        $username = (string)$this->request->input('username');
        $password = (string)$this->request->input('password');
        $user = $this->studentUserDao->login($username, $password);

        $token = $this->jwt->getToken($user);
        $data  = [
            'token' => (string) $token,
            'exp'   => $this->jwt->getTTL(),
            'user'  => $user
        ];
        return $this->response->success($data);
    }

    /**
     * @PostApi(path="find_password", description="找回密码")
     * @FormData(key="password|密码", rule="required")
     * @FormData(key="phone|手机号码", rule="required")
     * @FormData(key="captcha|手机验证码", rule="required")
     * @ApiResponse(code="0", description="成功")
     */
    public function find_password()
    {
        $params = $this->request->all();
        $validation = $this->validationFactory->make(
            $params,
            [
                'phone'         =>  'required',
                'password'      =>  'required|min:8',
                'captcha'       =>  'required',
            ],
            [
                'phone.required'         =>  '填写手机号',
                'password.min'         =>  '密码长度最少8位',
                'captcha.required'       =>  '请输入验证码',
            ]
        );
        if ($validation->fails()) {
            $errorMessage = $validation->errors()->first();
            throw new BusinessException(ErrorCode::COMMON_ERROR, $errorMessage);
        }
        $this->smsService->verify($params['phone'], SmsHelper::TYPE_FIND_PASS, $params['captcha']);  //校验验证码
        $result = $this->userService->updatePassword($params['phone'],$params['password']);
        return $this->response->success($result);
    }

    /**
     * @PostApi(path="send_sms", description="发送短信")
     * @FormData(key="phone|手机号码", rule="required")
     * @FormData(key="type|类型（find_pass，register，update_password，reset_phone）", rule="required")
     * @FormData(key="uuid|verify_code接口的uuid", rule="required")
     * @FormData(key="verify_code|图形验证码", rule="required")
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function send_sms()
    {
        $phone = $this->request->input('phone');
        $type = $this->request->input('type');
        if (!in_array($type, SmsHelper::sms_send_type())) {
            throw new BusinessException(ErrorCode::COMMON_ERROR, '短信类型错误');
        }

        $uuid = $this->request->input('uuid');
        $verify_code = $this->request->input('verify_code');
        $validation = $this->validationFactory->make(
            $this->request->all(),
            [
                'uuid'          =>  'required',
            ],
            [
                'uuid.required'          =>  '请提交uuid',
            ]
        );
        if ($validation->fails()) {
            $errorMessage = $validation->errors()->first();
            throw new BusinessException(ErrorCode::COMMON_ERROR, $errorMessage);
        }
        $vCode = cache()->get($uuid);
        if ($verify_code != $vCode) {
            throw new BusinessException(ErrorCode::COMMON_ERROR, "图片验证码错误");
        }
        $result = $this->smsService->send($phone, $type);
        cache()->delete($uuid);
        return $this->response->success($result, '短信发送成功，在10分钟内有效');
    }

    /**
     * @PostApi(path="verify_code", description="图片验证码")
     * @FormData(key="uuid|随机数", rule="required")
     * @ApiResponse(code="0", description="成功")
     */
    public function verify_code()
    {
        $uuid = $this->request->input('uuid');

        $validation = $this->validationFactory->make(
            $this->request->all(),
            [
                'uuid'          =>  'required',
            ],
            [
                'uuid.required'          =>  '请提交uuid',
            ]
        );
        if ($validation->fails()) {
            $errorMessage = $validation->errors()->first();
            throw new BusinessException(ErrorCode::COMMON_ERROR, $errorMessage);
        }

        $VCode =  make(VerifyCode::class);
        // 随机生成验证码
        $random = $captcha = rand(1000, 9999);
        cache()->set($uuid, $random, 300);
        $code = $VCode->DrawCode($random);
        return $this->response->success($code->getImageBase64());
    }

}