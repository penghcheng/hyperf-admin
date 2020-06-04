<?php

declare (strict_types=1);
namespace App\Model;

//use Hyperf\DbConnection\Model\Model;
/**
 * @property string $uuid 
 * @property string $code 
 * @property string $expire_time 
 */
class SysCaptcha extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_captcha';
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
    protected $casts = [];
}