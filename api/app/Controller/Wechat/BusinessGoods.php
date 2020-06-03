<?php


namespace App\Controller\Wechat;


use App\Constants\ErrorCode;
use App\Controller\AbstractController;
use App\Exception\BusinessException;
use App\Service\BusinessBindSchoolService;
use App\Service\BusinessService;
use App\Service\SchoolService;
use Hyperf\Apidog\Annotation\ApiController;
use Hyperf\Apidog\Annotation\ApiResponse;
use Hyperf\Apidog\Annotation\PostApi;
use Hyperf\Di\Annotation\Inject;

/**
 * @ApiController(tag="商品以及规格相关", prefix="we/business_goods", description="")
 */
class BusinessGoods extends AbstractController
{

    /**
     * @Inject()
     * @var SchoolService
     */
    private $schoolService;

    /**
     * @Inject()
     * @var BusinessBindSchoolService
     */
    private $businessBindSchoolService;

    /**
     * @Inject()
     * @var BusinessService
     */
    private $businessService;

    /**
     * @PostApi(path="goods_lists", description="获取所有商品列表")
     * @ApiResponse(code="-1", description="参数错误")
     * @ApiResponse(code="0", description="成功", schema={"id":1})
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function goods_lists()
    {
        $studentId = $this->request->getAttribute('user')['student_id'];
        $schoolStudentInfo = $this->schoolService->getSchoolInfoByStudentId($studentId);
        if (!$schoolStudentInfo){
            throw new BusinessException(ErrorCode::COMMON_ERROR,'未绑定学校信息');
        }
        $schoolId = $schoolStudentInfo['school_id'];
        $year = date('Y',time());
        $businessBindSchoolInfo = $this->businessBindSchoolService->getBusinessIdBySchoolIdAndYear($schoolId,$year);
        if (empty($businessBindSchoolInfo)){
            throw new BusinessException(ErrorCode::COMMON_ERROR,'您的学校'.$year.'年度暂未与服装供应商绑定');
        }
        $businessBindSchoolId = $businessBindSchoolInfo['business_bind_school_id'];
        $goods = $this->businessService->getGoodsByBusinessBindSchoolId($businessBindSchoolId);
        return $this->response->success($goods);
    }

}