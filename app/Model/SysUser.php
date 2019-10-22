<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $user_id
 * @property string $username
 * @property string $password
 * @property string $salt
 * @property string $email
 * @property string $mobile
 * @property int $status
 * @property int $create_user_id
 * @property string $create_time
 */
class SysUser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["username","password","email","mobile","salt","status","create_user_id"];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['user_id' => 'integer', 'status' => 'integer', 'create_user_id' => 'integer'];
}