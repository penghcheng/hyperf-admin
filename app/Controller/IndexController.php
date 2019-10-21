<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use App\Model\SysUser;
use App\Service\Instance\JwtInstance;

class IndexController extends AbstractController
{
    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

        $sysUser= SysUser::query()->where("user_id",1)->first();
        $token = JwtInstance::instance()->encode($sysUser);

        return $this->response->success([
            'method' => $method,
            'message' => "Hello {$user}.",
            'token' => $token
        ]);
    }
}
