<?php


namespace App\Controller\Admin;


use App\Constants\ErrorCode;
use App\Controller\AbstractController;
use App\Exception\BusinessException;
use App\Model\Business;
use App\Model\BusinessGood;
use App\Model\OssFile;
use App\Model\SchoolCampu;
use App\Service\BusinessBindSchoolService;
use App\Service\SchoolService;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Apidog\Annotation\ApiController;
use Hyperf\Apidog\Annotation\ApiResponse;
use Hyperf\Apidog\Annotation\FormData;
use Hyperf\Apidog\Annotation\Header;
use Hyperf\Apidog\Annotation\PostApi;
use App\Middleware\Admin\AdminAuthMiddleware;

/**
 * @ApiController(tag="供应商绑定学校",prefix="business_bind_school",description="供应商绑定学校等")
 * @Middlewares({
 *      @Middleware(AdminAuthMiddleware::class)
 *     })
 */
class BusinessBindSchoolController extends AbstractController
{

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    /**
     * @Inject()
     * @var BusinessBindSchoolService
     */
    protected $businessBindSchoolService;

    /**
     * @Inject()
     * @var SchoolService
     */
    protected $schoolService;

    /**
     * @PostApi(path="get_school_lists", description="获取学校列表")
     * @Header(key="token|接口访问凭证", rule="required")
     * @FormData(key="province_id|省份", rule="required")
     * @FormData(key="city_id|市", rule="required")
     * @FormData(key="school_type|学校类型", rule="required")
     * @FormData(key="keyword|学校名称", rule="sometimes")
     * @ApiResponse(code="-1", description="参数错误")
     * @ApiResponse(code="0", description="成功", schema={"id":1})
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get_school_lists()
    {
        $params = $this->request->all();
        $validator = $this->validationFactory->make(
            $params,
            [
                'province_id' => 'required',
                'city_id' => 'required',
                'school_type' => 'required',
            ],
            [
                'province_id.required' => '请选择省份',
                'city_id.required' => '请选择市',
                'school_type.required' => '请选择学校类型'
            ]
        );
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(ErrorCode::COMMON_ERROR, $errorMessage);
        }
        $data = $this->businessBindSchoolService->getSchoolLists($params);
        return $this->response->success($data);
    }

    /**
     * @PostApi(path="get_campus_lists", description="获取校区列表")
     * @Header(key="token|接口访问凭证", rule="required")
     * @FormData(key="school_id|school_id", rule="required")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get_campus_lists()
    {
        $schoolId = $this->request->input('school_id');
        if (empty($schoolId)) {
            return $this->response->success(array());
        }

        $data = $this->schoolService->getSchoolCampuList($schoolId, ['id','name']);

        return $this->response->success($data);
    }

    /**
     * @PostApi(path="bind_apply", description="申请绑定学校")
     * @Header(key="token|接口访问凭证", rule="required")
     * @FormData(key="school_id|学校id", rule="required")
     * @FormData(key="campus_id|校区id", rule="required")
     * @FormData(key="year|年份", rule="required")
     * @FormData(key="buy_type|购买方式", rule="required")
     * @ApiResponse(code="-1", description="参数错误")
     * @ApiResponse(code="0", description="成功", schema={"business_bind_school_id":1})
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function bind_apply()
    {
        $params = $this->request->all();
        $businessId = $this->request->getAttribute('user')['business_id'];

        $validator = $this->validationFactory->make(
            $params,
            [
                'school_id' => 'required',
                'year' => 'required',
                'buy_type' => 'required'
            ],
            [
                'school_id.required' => '未选择学校',
                'year.required' => '未选择年份',
                'buy_type.required' => '未选择购买方式'
            ]
        );
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(ErrorCode::COMMON_ERROR, $errorMessage);
        }
        if ($params['year']<date('Y')){
            throw new BusinessException(ErrorCode::COMMON_ERROR,'年份选择不可小于当前年份');
        }
        $params['business_id'] = $businessId;
        $result = $this->businessBindSchoolService->saveApply($params);
        if ($result) {
            return $this->response->success(['business_bind_school_id' => $result]);
        } else {
            throw new BusinessException(ErrorCode::COMMON_ERROR, '绑定失败，服务器异常');
        }
    }

    /**
     * @PostApi(path="business_info", description="获取供应商信息")
     * @Header(key="token|接口访问凭证", rule="required")
     * @ApiResponse(code="-1", description="参数错误")
     * @ApiResponse(code="0", description="成功", schema={"id":1})
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function business_info()
    {
        $businessId = $this->request->getAttribute('user')['business_id'];
        $info = Business::query()->where('id', $businessId)->first();
        if (empty($info)) {
            throw new BusinessException(ErrorCode::COMMON_ERROR, '获取信息失败，登录信息错误');
        }
        $info['business_license_url'] = !empty($info['business_license_id']) ? config('oss_url') . OssFile::query()->where('id', $info['business_license_id'])->value('path') : '';
        $info['certificate_img_url'] = !empty($info['certificate_img_id']) ? config('oss_url') . OssFile::query()->where('id', $info['certificate_img_id'])->value('path') : '';
        $info['id_card_img_url'] = !empty($info['id_card_img_id']) ? config('oss_url') . OssFile::query()->where('id', $info['id_card_img_id'])->value('path') : '';
        $info['id_card_back_img_url'] = !empty($info['id_card_back_img_id']) ? config('oss_url') . OssFile::query()->where('id', $info['id_card_back_img_id'])->value('path') : '';

        return $this->response->success($info);
    }

    /**
     * @PostApi(path="confirm_info", description="填写供应商信息")
     * @Header(key="token|接口访问凭证", rule="required")
     * @FormData(key="id|id", rule="required")
     * @FormData(key="business_license_id|营业执照上传的ossid", rule="required")
     * @FormData(key="certificate_img_id|税务登记证照上传的ossid", rule="required")
     * @FormData(key="id_card_img_id|法人身份证复印件正面上传的ossid", rule="required")
     * @FormData(key="id_card_back_img_id|法人身份证复印件反面上传的ossid", rule="required")
     * @ApiResponse(code="-1", description="参数错误")
     * @ApiResponse(code="0", description="成功", schema={"id":1})
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function confirm_info()
    {
        $params = $this->request->all();
        $validator = $this->validationFactory->make(
            $params,
            [
                'id' => 'required',
                'business_license_id' => 'required',
                'certificate_img_id' => 'required',
                'id_card_img_id' => 'required',
                'id_card_back_img_id' => 'required'
            ],
            [
                'id.required' => '无效id参数',
                'business_license_id.required' => '未上传营业执照',
                'certificate_img_id.required' => '未上传税务登记证照',
                'id_card_img_id.required' => '未上传法人身份证复印件正面照',
                'id_card_back_img_id.required' => '未上传法人身份证复印件反面照'
            ]
        );
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(ErrorCode::COMMON_ERROR, $errorMessage);
        }
        $businessId = $this->request->getAttribute('user')['business_id'];
        if ($params['id'] != $businessId) {
            throw new BusinessException(ErrorCode::COMMON_ERROR, '请勿越权违法操作');
        }

        $info = Business::query()->where('id', $businessId)->first();
        if ($info['business_license_id'] == $params['business_license_id'] &&
            $info['certificate_img_id'] == $params['certificate_img_id'] &&
            $info['id_card_img_id'] == $params['id_card_img_id'] &&
            $info['id_card_back_img_id'] == $params['id_card_back_img_id']
        ) return $this->response->success();

        $result = Business::query()->where('id', $params['id'])->update($params);
        if ($result) {
            return $this->response->success();
        } else {
            throw new BusinessException(ErrorCode::COMMON_ERROR, '服务器异常，稍后重试');
        }
    }

    /**
     * @PostApi(path="add_goods", description="填写商品信息")
     * @Header(key="token|接口访问凭证", rule="required")
     * @FormData(key="goods['business_bind_school_id']|bind_apply接口返回的id", rule="required")
     * @FormData(key="goods['name']|商品名称", rule="required")
     * @FormData(key="goods['price']|商品价格", rule="required")
     * @FormData(key="goods['is_must']|商品是否必购", rule="required")
     * @FormData(key="goods['imgs']|商品图片id（‘1,2,3’）", rule="required")
     * @FormData(key="goods['content']|商品详情", rule="required")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function add_goods()
    {
        $params = $this->request->all();

        Db::beginTransaction();
        try {
            if (!empty($params['goods'])) {
                foreach ($params['goods'] as $value) {
                    $validator = $this->validationFactory->make(
                        $value,
                        [
                            'business_bind_school_id' => 'required',
                            'name' => 'required',
                            'price' => 'required',
                            'is_must' => 'required',
                        ],
                        [
                            'business_bind_school_id.required' => '绑定学校信息错误',
                            'name.required' => '请输入商品名称',
                            'price.required' => '请输入商品价格',
                            'is_must.required' => '选择是否必购',
                        ]
                    );
                    if ($validator->fails()) {
                        $errorMessage = $validator->errors()->first();
                        throw new BusinessException(ErrorCode::COMMON_ERROR, $errorMessage);
                    }

                    $value['business_id'] = $this->request->getAttribute('user')['business_id'];
                    $value['create_time'] = time();
                    $value['update_time'] = time();

                    BusinessGood::query()->insertGetId($value);
                }
                Db::commit();
                return $this->response->success('', '添加商品成功');
            }
            throw new BusinessException(ErrorCode::COMMON_ERROR, '无商品数据添加');
        } catch (\Throwable $e) {
            Db::rollBack();
            throw new BusinessException(ErrorCode::COMMON_ERROR, $e->getMessage());
        }
    }

    /**
     * @PostApi(path="apply_lists", description="主页列表")
     * @Header(key="token|接口访问凭证", rule="required")
     * @FormData(key="province_id|学校所在省id", rule="sometimes")
     * @FormData(key="city_id|学校所在市id", rule="sometimes")
     * @FormData(key="school_type|学校类型", rule="sometimes")
     * @FormData(key="status|审核状态", rule="sometimes")
     * @FormData(key="buy_type|购买方式", rule="sometimes")
     * @FormData(key="year|年份", rule="sometimes")
     * @ApiResponse(code="-1", description="参数错误")
     * @ApiResponse(code="0", description="成功", schema={"id":1})
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function apply_lists()
    {
        $params = $this->request->all();
        $params['business_id'] = $this->request->getAttribute('user')['business_id'];
        $params['page_size'] = $this->request->input('page_size',15);
        $lists = $this->businessBindSchoolService->getBindLists($params);
        return $this->response->success($lists);
    }

}