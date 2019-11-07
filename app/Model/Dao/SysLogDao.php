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
use App\Model\SysLog;
use Hyperf\DbConnection\Db;

class SysLogDao
{
    public function first($id, $throw = true)
    {
        $model = SysLog::query()->where('id', $id)->first();
        if (empty($model) && $throw) {
            throw new BusinessException(ErrorCode::USER_NOT_EXIST);
        }
        return $model;
    }

    /**
     * 根据条件获取totalCount
     * @param string $key
     * @return int
     */
    public function getTotalCount(string $key): int
    {
        $where = ['like', "'%" . $key . "%'"];

        $count = SysLog::query()->where('username', $where)->orWhere("operation", $where)->count();

        return $count;
    }
}
