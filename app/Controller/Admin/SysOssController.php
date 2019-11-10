<?php
/**
 * Created by PhpStorm.
 * User: derek
 * Date: 2019/11/10
 * Time: 22:05
 */

namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Service\CommonService;
use App\Service\Instance\JwtInstance;
use Hyperf\Di\Annotation\Inject;

class SysOssController extends AbstractController
{

    /**
     * @Inject()
     * @var CommonService
     */
    protected $commonService;

    /**
     * sys/oss/list
     * OSS列表
     */
    public function sysOssList()
    {
        $currentLoginUserId = JwtInstance::instance()->build()->getId();
        $page = (int)$this->request->input('page');
        $limit = (int)$this->request->input('limit');
        $result = $this->commonService->getSysLogList($limit, $page);

        return $this->response->success([
            'page' => $result
        ]);
    }

    /**
     * sys/oss/delete
     * oss删除
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sysOssDelete()
    {
        $currentLoginUserId = JwtInstance::instance()->build()->getId();

        $params = $this->request->post();
        if (!is_array($params) || empty($params)) {
            return $this->response->error("提交错误");
        }
        $result = $this->commonService->sysOssDelete($params);
        if ($result) {
            return $this->response->success();
        } else {
            return $this->response->error("删除失败");
        }
    }
}