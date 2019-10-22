<?php

declare(strict_types=1);

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
            'roleIdList' => $model->roleIdList ?? null,
            'password' => $model->password,
            'mobile' => $model->mobile,
            'email' => $model->email,
            'createUserId' => $model->create_user_id,
            'createTime' => $model->create_time,
        ];
    }

    public function forArray($model)
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

    public function arrayFormat($models)
    {
        $result = [];
        foreach ($models as $model) {
            $item = self::forArray($model);
            $result[] = $item;
        }
        return $result;
    }

    public function collectionFormat($models)
    {
        $result = [];
        foreach ($models as $model) {
            $item = self::base($model);
            $result[] = $item;
        }
        return $result;
    }
}
