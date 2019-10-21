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

namespace App\Service\Formatter;

use App\Model\SysUser;

class SysUserFormatter extends Formatter
{
    public function base(SysUser $model)
    {
        return [
            'userId' => $model->user_id,
            'username' => $model->username,
            'status' => $model->status,
            'salt' => $model->salt,
            'roleIdList' => null,
            'password' => $model->password,
            'mobile' => $model->mobile,
            'email' => $model->email,
            'createUserId' => $model->create_user_id,
            'createTime' => $model->create_time,
        ];
    }

    public function arr($model)
    {
        return [
            'userId' => $model['user_id'],
            'username' => $model['username'],
            'status' => $model['status'],
            'salt' => $model['salt'],
            'roleIdList' => null,
            'password' => $model['password'],
            'mobile' => $model['mobile'],
            'email' => $model['email'],
            'createUserId' => $model['create_user_id'],
            'createTime' => $model['create_time'],
        ];
    }

    public function formatArr($models)
    {
        $result = [];
        foreach ($models as $model) {
            $item = self::arr($model);
            $result[] = $item;
        }
        return $result;
    }
}
