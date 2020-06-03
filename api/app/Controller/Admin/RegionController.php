<?php


namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Service\RegionService;
use Hyperf\Apidog\Annotation\ApiController;
use Hyperf\Apidog\Annotation\GetApi;
use Hyperf\Apidog\Annotation\PostApi;
use Hyperf\Di\Annotation\Inject;

/**
 * @ApiController(tag="省市区地区",prefix="region",description="省市区")
 * Class RegionController
 * @package App\Controller\Admin
 */
class RegionController extends AbstractController
{
    /**
     * @Inject()
     * @var RegionService
     */
    protected $regionService;

    /**
     * @PostApi(path="tree")
     * @GetApi(path="tree")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function tree()
    {
        $data = $this->regionService->getTree();
        return $this->response->success($data);
    }

}