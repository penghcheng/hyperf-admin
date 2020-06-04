<?php

declare (strict_types=1);
namespace App\Model;

//use Hyperf\DbConnection\Model\Model;
/**
 * @property int $user_id 
 * @property string $token 
 * @property string $expire_time 
 * @property string $update_time 
 */
class SysUserToken extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_user_token';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['user_id' => 'integer'];
}