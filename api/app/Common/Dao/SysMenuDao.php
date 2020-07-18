<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/5
 * Time: 9:10
 */

namespace App\Common\Dao;


use Hyperf\DbConnection\Db;

class SysMenuDao extends BaseDao
{
    /**
     * 自身关联
     * @param array $menu_ids
     * @param $l_feilds
     * @param $r_feilds
     * @param string $type
     * @return array
     */
    public function selfJoinSelf($menu_ids = [], $l_feilds, $r_feilds, $type = "LEFT")
    {
        $sys_menus = Db::select("SELECT " . $l_feilds . "," . $r_feilds . " FROM sys_menu l " . $type . " JOIN sys_menu r ON l.parent_id = r.menu_id where l.menu_id in (" . implode(',', $menu_ids) . ") order by l.order_num ASC ;");
        return $sys_menus;
    }
}