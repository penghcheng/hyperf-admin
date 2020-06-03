<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/5/25
 * Time: 9:41
 */

namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Service\UploadService;
use Hyperf\Apidog\Annotation\ApiController;
use Hyperf\Apidog\Annotation\ApiResponse;
use Hyperf\Apidog\Annotation\FormData;
use Hyperf\Apidog\Annotation\GetApi;
use Hyperf\Apidog\Annotation\Header;
use Hyperf\Apidog\Annotation\PostApi;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\Admin\AdminAuthMiddleware;

/**
 * @ApiController(tag="上传文件",prefix="file",description="上传文件")
 * @Middlewares({
 *      @Middleware(AdminAuthMiddleware::class)
 *     })
 */
class OssController extends AbstractController
{

    /**
     * @Inject()
     * @var UploadService
     */
    private $uploadService;

    /**
     * @PostApi(path="upload", description="上传文件")
     * @Header(key="token|接口访问凭证", rule="required")
     * @FormData(key="file|file", rule="required")
     * @ApiResponse(code="-1", description="参数错误")
     * @ApiResponse(code="0", description="成功")
     */
    public function upload()
    {
        $user = $this->request->getAttribute("user");
        $user_id = $user['user_id'];

        $file = $this->request->file('file');

        $result = $this->uploadService->ossUpload($file, $user_id);

        return $this->response->success(['file_id'=>$result]);
    }

    /**
     * @GetApi(path="see", description="查看上传文件")
     * @Header(key="token|接口访问凭证", rule="required")
     * @FormData(key="file_ids|file", rule="required")
     * @ApiResponse(code="-1", description="参数错误")
     * @ApiResponse(code="0", description="成功")
     */
    public function file()
    {
        $user = $this->request->getAttribute("user");
        $file_ids = $this->request->input('file_ids');
        $result = $this->uploadService->ossFile($file_ids);

        return $this->response->success($result);
    }

}