<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: penghcheng
 * Date: 2019/10/21 0021
 * Time: 10:04
 */

namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Service\Instance\JwtInstance;
use App\Service\SysUserService;
use Hyperf\Di\Annotation\Inject;


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

}