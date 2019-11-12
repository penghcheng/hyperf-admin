<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/7 0007
 * Time: 10:32
 */

namespace App\Service\Formatter;


use App\Model\SysLog;

class SysLogFormatter extends Formatter
{
    public function base(SysLog $model)
    {
        return [
            'id' => $model->id,
            'ip' => $model->ip,
            'method' => $model->method,
            'username' => $model->username ?? null,
            'operation' => $model->operation,
            'params' => $model->params,
            'time' => $model->time,
            'createDate' => $model->create_date
        ];
    }

    public function forArray($model)
    {
        return [
            'id' => $model['id'],
            'ip' => $model['ip'],
            'method' => $model['method'],
            'username' => $model['username'] ?? null,
            'operation' => $model['operation'],
            'params' => $model['params'],
            'time' => $model['time'],
            'createDate' => $model['create_date']
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