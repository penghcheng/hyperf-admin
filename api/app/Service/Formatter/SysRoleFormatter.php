<?php

declare(strict_types=1);

namespace App\Service\Formatter;

use App\Model\SysRole;

class SysRoleFormatter extends Formatter
{
    public function base(SysRole $model)
    {
        return [
            'roleId' => $model->role_id,
            'roleName' => $model->role_name,
            'remark' => $model->remark,
            'menuIdList' => $model->menuIdList ?? null,
            'createUserId' => $model->create_user_id,
            'createTime' => $model->create_time
        ];
    }

    public function forArray($model)
    {
        return [
            'roleId' => $model['role_id'],
            'roleName' => $model['role_name'],
            'remark' => $model['remark'],
            'menuIdList' => $model['menuIdList'] ?? null,
            'createUserId' => $model['create_user_id'],
            'createTime' => $model['create_time']
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
