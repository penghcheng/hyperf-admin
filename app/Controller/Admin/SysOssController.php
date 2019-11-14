<?php
/**
 * Created by PhpStorm.
 * User: derek
 * Date: 2019/11/10
 * Time: 22:05
 */

namespace App\Controller\Admin;


use App\Annotation\SysLogAnnotation;
use App\Controller\AbstractController;
use App\Kernel\Log\Log;
use App\Service\CommonService;
use App\Service\Instance\JwtInstance;
use Hyperf\Di\Annotation\Inject;
use OSS\Core\OssException;
use OSS\OssClient;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

/**
 * @SysLogAnnotation()
 */
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

    /**
     * 获取配置
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sysOssConfig()
    {
        $currentLoginUserId = JwtInstance::instance()->build()->getId();
        try {
            $result = $this->commonService->sysOssConfig();
            return $this->response->success(['config' => json_decode($result->param_value, true)]);
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage());
        }
    }

    /**
     * 保存oss配置
     */
    public function sysOssSaveConfig()
    {
        $currentLoginUserId = JwtInstance::instance()->build()->getId();
        $type = (int)$this->request->input('type');
        $aliyunAccessKeyId = (string)$this->request->input('aliyunAccessKeyId');
        $aliyunAccessKeySecret = (string)$this->request->input('aliyunAccessKeySecret');
        $aliyunBucketName = (string)$this->request->input('aliyunBucketName');

        $aliyunDomain = (string)$this->request->input('aliyunDomain');
        $aliyunEndPoint = (string)$this->request->input('aliyunEndPoint');
        $aliyunPrefix = (string)$this->request->input('aliyunPrefix');
        $qcloudBucketName = (string)$this->request->input('qcloudBucketName');
        $qcloudDomain = (string)$this->request->input('qcloudDomain');
        $qcloudPrefix = (string)$this->request->input('qcloudPrefix');
        $qcloudSecretId = (string)$this->request->input('qcloudSecretId');
        $qcloudSecretKey = (string)$this->request->input('qcloudSecretKey');
        $qiniuAccessKey = (string)$this->request->input('qiniuAccessKey');
        $qiniuBucketName = (string)$this->request->input('qiniuBucketName');
        $qiniuDomain = (string)$this->request->input('qiniuDomain');
        $qiniuPrefix = (string)$this->request->input('qiniuPrefix');
        $qiniuSecretKey = (string)$this->request->input('qiniuSecretKey');

        $localServerDomain = (string)$this->request->input('localServerDomain');
        $localServerPrefix = (string)$this->request->input('localServerPrefix');
        $localServerPath = (string)$this->request->input('localServerPath');

        $params = [
            'type' => $type,
            'aliyunAccessKeyId' => $aliyunAccessKeyId,
            'aliyunAccessKeySecret' => $aliyunAccessKeySecret,
            'aliyunBucketName' => $aliyunBucketName,
            'aliyunDomain' => $aliyunDomain,
            'aliyunEndPoint' => $aliyunEndPoint,
            'aliyunPrefix' => $aliyunPrefix,
            'qcloudBucketName' => $qcloudBucketName,
            'qcloudDomain' => $qcloudDomain,
            'qcloudPrefix' => $qcloudPrefix,
            'qcloudSecretId' => $qcloudSecretId,
            'qcloudSecretKey' => $qcloudSecretKey,
            'qiniuAccessKey' => $qiniuAccessKey,
            'qiniuBucketName' => $qiniuBucketName,
            'qiniuDomain' => $qiniuDomain,
            'qiniuPrefix' => $qiniuPrefix,
            'qiniuSecretKey' => $qiniuSecretKey,
            'localServerDomain' => $localServerDomain,
            'localServerPrefix' => $localServerPrefix,
            'localServerPath' => $localServerPath
        ];

        $result = $this->commonService->sysOssSaveConfig($params);
        if ($result) {
            return $this->response->success();
        } else {
            return $this->response->error("保存失败");
        }
    }

    /**
     * oss上传文件
     */
    public function sysOssUpload()
    {
        $currentLoginUserId = JwtInstance::instance()->build()->getId();

        $result = $this->commonService->sysOssConfig();
        $config = json_decode($result->param_value, true);

        $file = $this->request->file('file');

        $localServerPath = !empty($config['localServerPath']) ? $config['localServerPath'] : '/hyperf-upload';
        $localServerPrefix = !empty($config['localServerPrefix']) ? $config['localServerPrefix'] . '/' : '';
        $dateDir = date("Ym/d", time());
        $uploadPath = $localServerPrefix . $dateDir;

        if (!is_dir($localServerPath . "/" . $uploadPath)) {
            mkdir($localServerPath . "/" . $uploadPath, 0777, true);
        }
        $fileName = $localServerPath . "/" . $uploadPath . "/" . $file->getClientFilename();
        try {
            $file->moveTo($fileName);
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage());
        }
        $result = false;
        //七牛云
        if (!empty($config) && $config['type'] == 1) {

            if (empty($config['qiniuAccessKey'])) {
                return $this->response->error("请设置七牛云的oss配置");
            }

            try {
                $result = $this->commonService->uploadQiniu($config['qiniuDomain'], $config['qiniuAccessKey'], $config['qiniuSecretKey'], $config['qiniuBucketName'], $config['qiniuPrefix'], $file->getClientFilename(), $fileName);
            } catch (\Exception $e) {
                return $this->response->error($e->getMessage());
            }
        }

        // 阿里云
        if (!empty($config) && $config['type'] == 2) {

            if (empty($config['aliyunAccessKeyId'])) {
                return $this->response->error("请设置阿里云的oss配置");
            }

            try {
                $result = $this->commonService->uploadAliyun($config['aliyunAccessKeyId'], $config['aliyunAccessKeySecret'], $config['aliyunEndPoint'], $config['aliyunBucketName'], $config['aliyunPrefix'], $file->getClientFilename(), $fileName);
            } catch (\Exception $e) {
                return $this->response->error($e->getMessage());
            }
        }

        // 本地
        if (!empty($config) && $config['type'] == 4) {
            $url = $config['localServerDomain'] . "/" . $uploadPath . "/" . $file->getClientFilename();
            $data = [
                'url' => $url,
                'create_date' => date("Y-m-d h:i:s", time())
            ];
            $result = $this->commonService->sysOssSave($data);
        }

        if ($result) {
            return $this->response->success();
        } else {
            return $this->response->error("上传失败");
        }
    }
}