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
use App\Service\Instance\JwtInstance;
use App\Service\SysUserService;
use Hyperf\Di\Annotation\Inject;

/**
 * @SysLogAnnotation()
 */
class SysRoleController extends AbstractController
{

    /**
     * @Inject
     * @var SysUserService
     */
    protected $sysUserService;


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
     * sys/role/save
     * 新增角色
     */
    public function sysRoleSave()
    {
        $userId = JwtInstance::instance()->build()->getId();

        $roleName = (string)$this->request->input('roleName');
        $remark = (string)$this->request->input('remark');
        $menuIdList = $this->request->input('menuIdList');

        $result = $this->sysUserService->sysRoleSave($userId, $roleName, $remark, $menuIdList);
        if ($result) {
            return $this->response->success();
        } else {
            return $this->response->error('保存失败');
        }
    }


    /**
     * sys/role/info/{id:\d+}
     * 获取角色信息
     */
    public function sysRoleInfo($id)
    {
        $userId = JwtInstance::instance()->build()->getId();

        $result = $this->sysUserService->getSysRoleInfo($id);

        if ($result) {
            return $this->response->success(['role' => $result]);
        } else {
            return $this->response->error('保存失败');
        }
    }

    /**
     * sys/role/update
     * update角色管理
     */
    public function sysRoleUpdate()
    {

        $userId = JwtInstance::instance()->build()->getId();

        $roleId = (int)$this->request->input('roleId');
        $roleName = (string)$this->request->input('roleName');
        $remark = (string)$this->request->input('remark');
        $menuIdList = $this->request->input('menuIdList');

        $result = $this->sysUserService->sysRoleSave($userId, $roleName, $remark, $menuIdList,'update',$roleId);

        if ($result) {
            return $this->response->success();
        } else {
            return $this->response->error('保存失败');
        }
    }


    /**
     * 删除角色
     * url:sys/role/delete
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sysRoleDelete()
    {

        $userId = JwtInstance::instance()->build()->getId();

        $params = $this->request->post();

        if (!is_array($params) || empty($params)) {
            return $this->response->error("提交错误");
        }

        $result = $this->sysUserService->sysRoleDelete($params,$userId);

        if ($result) {
            return $this->response->success();
        } else {
            return $this->response->error("删除失败");
        }
    }

}