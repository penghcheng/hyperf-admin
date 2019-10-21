<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/21 0021
 * Time: 10:54
 */

namespace App\Service;


use App\Model\Dao\SysUserDao;
use Hyperf\Di\Annotation\Inject;

class SysUserService extends Service
{

    /**
     * @Inject()
     *
     * @var SysUserDao
     */
    protected $sysUserDao;

    public function getNemuNav(int $user_id):array
    {
        $roleModel = null;
        if($user_id !=1){
            $roleModel = $this->sysUserDao->getUserRole($user_id);
        }
        return $this->sysUserDao->getUserMenusPermissions($user_id != 1 ? $roleModel['role_id']: 0,$user_id);
    }

}