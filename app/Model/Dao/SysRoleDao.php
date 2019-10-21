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

namespace App\Model\Dao;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Model\SysRole;
use Hyperf\DbConnection\Db;

class SysRoleDao
{
    public function first($role_id, $throw = true)
    {
        $model = SysRole::query()->where('role_id', $role_id)->first();
        if (empty($model) && $throw) {
            throw new BusinessException(ErrorCode::USER_NOT_EXIST);
        }
        return $model;
    }

    public function getOne($roleName, $throw = true)
    {
        $model = SysRole::query()->where('username', $roleName)->first();
        if (empty($model) && $throw) {
            throw new BusinessException(ErrorCode::USER_NOT_EXIST);
        }
        return $model;
    }

    /**
     * 根据条件获取totalCount
     * @param int $user_id 创建人id
     * @param string $roleName
     * @return int
     */
    public function getTotalCount(int $user_id = 1, string $roleName): int
    {
        $where = [];
        if (!empty($roleName)) {
            $where['role_name'] = ['like', "'%" . $roleName . "%'"];
        }

        if ($user_id == 1) {
            $count = Db::table('sys_role')->where($where)->count();
        } else {
            $where['create_user_id'] = $user_id;
            $count = Db::table('sys_role')->where($where)->count();
        }
        return $count;
    }
}
