<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/7 0007
 * Time: 10:32
 */

namespace App\Service\Formatter;


use App\Model\SysLog;

class SysOssFormatter extends Formatter
{
    public function base(SysLog $model)
    {
        return [
            'id' => $model->id,
            'url' => $model->url,
            'createDate' => $model->create_date
        ];
    }

    public function forArray($model)
    {
        return [
            'id' => $model['id'],
            'url' => $model['url'],
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