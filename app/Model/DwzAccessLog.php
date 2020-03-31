<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property string $s4id 
 * @property int $ip 
 * @property string $user_agent 
 * @property \Carbon\Carbon $create_time 
 * @property \Carbon\Carbon $update_time 
 */
class DwzAccessLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dwz_access_logs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //    protected $fillable = [];
    protected $guarded = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'int', 'ip' => 'integer', 'create_time' => 'datetime', 'update_time' => 'datetime'];
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
}