<?php

declare(strict_types=1);


namespace App\Service\Formatter;

use App\Model\SysMenu;

class SysMenuFormatter extends Formatter
{
    public function base(SysMenu $model)
    {
        return [
            'menuId' => $model->menu_id,
            'parentId' => $model->parent_id,
            'parentName' => null,
            'name' => $model->name,
            'url' => $model->url,
            'perms' => $model->perms,
            'type' => $model->type,
            'icon' => $model->icon,
            'url' => $model->url,
            'orderNum' => $model->order_num,
            'list' => null
        ];
    }

    public function arr($model)
    {
        return [
            'menuId' => $model['menu_id'],
            'parentId' => $model['parent_id'],
            'parentName' => null,
            'name' => $model['name'],
            'url' => $model['url'],
            'perms' => $model['perms'],
            'type' => $model['type'],
            'icon' => $model['icon'],
            'url' => $model['url'],
            'orderNum' => $model['order_num'],
            'list' => null
        ];
    }

    public function formatArray($models)
    {
        $result = [];
        foreach ($models as $model) {
            $item = self::base($model);
            $result[] = $item;
        }
        return $result;
    }
}
