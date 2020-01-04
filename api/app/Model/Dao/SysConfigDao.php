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
use App\Model\SysConfig;

class SysConfigDao
{
    public function first($id, $throw = true)
    {
        $model = SysConfig::query()->where('id', $id)->first();
        if (empty($model) && $throw) {
            throw new BusinessException(ErrorCode::USER_NOT_EXIST);
        }
        return $model;
    }

    public function firstOrNew(array $data, $throw = true)
    {
        if (!isset($data['id'])) {
            $config = SysConfig::query()->where("param_key", $data['param_key'])->first();
            if (!empty($config)) {
                throw new BusinessException(0, '数据库中已存在该记录');
            }
            $result = SysConfig::query()->insert($data);
        } else {
            $config = SysConfig::query()->where("id", '<>', $data['id'])->where("param_key", $data['param_key'])->first();
            if (!empty($config)) {
                throw new BusinessException(0, '数据库中已存在该记录');
            }
            $result = SysConfig::query()->where('id', $data['id'])->update($data);
        }
        if (empty($result) && $throw) {
            throw new BusinessException(0, '保存失败');
        }
        return $result;
    }

    /**
     * 根据条件获取totalCount
     * @param string $key
     * @return int
     */
    public function getTotalCount(string $key): int
    {
        $count = SysConfig::query()->where("status", 1)->where('param_key', 'like', "%".$key."%")->orWhere("remark", 'like', "%".$key."%")->count();
        return $count;
    }
}
