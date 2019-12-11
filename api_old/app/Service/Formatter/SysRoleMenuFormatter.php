<?php

declare(strict_types=1);


namespace App\Service\Formatter;

use App\Model\SysRoleMenu;

class SysRoleMenuFormatter extends Formatter
{
    public function base(SysRoleMenu $model)
    {
        return [
            'id' => $model->id,
            'menu_id' => $model->menu_id,
            'role_id' => $model->role_id,
        ];
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
