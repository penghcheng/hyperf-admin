<?php


namespace App\Controller\Wechat;


use App\Controller\AbstractController;
use App\Factory\OfficialFactory;
use Hyperf\Apidog\Annotation\ApiController;
use Hyperf\Apidog\Annotation\GetApi;
use Hyperf\Apidog\Annotation\PostApi;

/**
 * @ApiController(tag="微信公众号",prefix="we/wechat",description="公众号")
 */
class OfficialWeChatController extends AbstractController
{

    protected $app;

    public function __construct(OfficialFactory $officialFactory)
    {
        $this->app = $officialFactory->create();
    }

    /**
     * @PostApi(path="test")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function test()
    {
        $response = $this->app->oauth->scopes(['snsapi_userinfo'])->redirect();
        return $this->response->success($response);
    }

    /**
     * @PostApi(path="oauth_callback")
     * @GetApi(path="oauth_callback")
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function oauth_callback()
    {
        $oauth = $this->app->oauth;

        $user = $oauth->user();
        $openId = $user->getId();
        cache()->set('wechat_user:'.$openId,$user->toArray());

//        $targetUrl = empty(cache()->get('target_url:'.$openId)) ? '/' : cache()->get('target_url:'.$openId);
        return $this->response->success(['open_id'=>$openId]);
    }

}