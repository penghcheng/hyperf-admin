<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/7 0007
 * Time: 10:32
 */

namespace App\Service\Formatter;


use App\Model\SysConfig;

class SysConfigFormatter extends Formatter
{
    public function base(SysConfig $model)
    {
        return [
            'id' => $model->id,
            'paramKey' => $model->param_key,
            'paramValue' => $model->param_value,
            'remark' => $model->remark
        ];
    }

    public function forArray($model)
    {
        return [
            'id' => $model['id'],
            'paramKey' => $model['param_key'],
            'paramValue' => $model['param_value'],
            'remark' => $model['remark']
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