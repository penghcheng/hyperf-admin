<?php
/**
 * Created by PhpStorm.
 * User: penghcheng
 * Date: 2019/11/7 0007
 * Time: 10:06
 */

namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Service\Instance\JwtInstance;
use App\Service\SysUserService;
use Hyperf\Di\Annotation\Inject;


class SysLogController extends AbstractController
{
    /**
     * @Inject()
     * @var SysUserService
     */
    protected $sysUserService;


    /**
     * 操作日志管理list
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sysLogList()
    {
        $currentLoginUserId = JwtInstance::instance()->build()->getId();

        $key = (string)$this->request->input('key');
        $page = (int)$this->request->input('page');
        $limit = (int)$this->request->input('limit');

        $result = $this->sysUserService->getSysLogList($key, $limit, $page);

        return $this->response->success([
            'page' => $result
        ]);
    }


}