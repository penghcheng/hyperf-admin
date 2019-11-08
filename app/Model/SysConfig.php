<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $param_key
 * @property string $param_value
 * @property int $status
 * @property string $remark
 */
class SysConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_config';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['param_key'];

    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'int', 'status' => 'integer'];
}