<?php
/**
 * Created by PhpStorm.
 * User: derek
 * Date: 2019/11/7
 * Time: 21:58
 */

namespace App\Controller\Admin;


use App\Annotation\SysLogAnnotation;
use App\Controller\AbstractController;
use App\Service\Instance\JwtInstance;
use App\Service\SysUserService;
use Hyperf\Di\Annotation\Inject;

/**
 * @SysLogAnnotation()
 */
class SysConfigController extends AbstractController
{
    /**
     * @Inject()
     * @var SysUserService
     */
    protected $sysUserService;

    /**
     * 参数列表
     * sys/config/list
     */
    public function sysConfigList()
    {
        $currentLoginUserId = JwtInstance::instance()->build()->getId();

        $paramKey = (string)$this->request->input('paramKey');
        $page = (int)$this->request->input('page');
        $limit = (int)$this->request->input('limit');

        $result = $this->sysUserService->getSysConfigList($paramKey, $limit, $page);

        return $this->response->success([
            'page' => $result
        ]);
    }

    /**
     * 新增参数
     * sys/config/save
     */
    public function sysConfigSave()
    {
        $currentLoginUserId = JwtInstance::instance()->build()->getId();
        $paramKey = (string)$this->request->input('paramKey');
        $paramValue = (string)$this->request->input('paramValue');
        $remark = (string)$this->request->input('remark');

        $result = $this->sysUserService->getSysConfigSave($paramKey, $paramValue, $remark, 0);
        if ($result === true) {
            return $this->response->success();
        } else {
            return $this->response->error($result);
        }
    }

    /**
     * 获取参数
     * sys/config/info/3
     */
    public function sysConfigInfo($id)
    {
        $currentLoginUserId = JwtInstance::instance()->build()->getId();
        $result = $this->sysUserService->getSysConfigInfo($id);
        if (is_array($result)) {
            return $this->response->success(['config' => $result]);
        } else {
            return $this->response->error($result);
        }
    }

    /**
     * update参数
     * sys/config/update
     */
    public function sysConfigUpdate()
    {
        $currentLoginUserId = JwtInstance::instance()->build()->getId();
        $paramKey = (string)$this->request->input('paramKey');
        $paramValue = (string)$this->request->input('paramValue');
        $remark = (string)$this->request->input('remark');
        $id = (string)$this->request->input('id');

        $result = $this->sysUserService->getSysConfigSave($paramKey, $paramValue, $remark, $id);
        if ($result === true) {
            return $this->response->success();
        } else {
            return $this->response->error($result);
        }
    }

    /**
     * sys/config/delete
     * 删除参数
     */
    public function sysConfigDelete()
    {
        $currentLoginUserId = JwtInstance::instance()->build()->getId();

        $params = $this->request->post();
        if (!is_array($params) || empty($params)) {
            return $this->response->error("提交错误");
        }
        $result = $this->sysUserService->sysConfigDelete($params);
        if ($result) {
            return $this->response->success();
        } else {
            return $this->response->error("删除失败");
        }
    }

}