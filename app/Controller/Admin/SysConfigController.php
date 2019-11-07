<?php
/**
 * Created by PhpStorm.
 * User: derek
 * Date: 2019/11/7
 * Time: 21:58
 */

namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Service\Instance\JwtInstance;
use App\Service\SysUserService;
use Hyperf\Di\Annotation\Inject;

class SysConfigController extends AbstractController
{
    /**
     * @Inject()
     * @var SysUserService
     */
    protected $sysUserService;

    /**
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

}