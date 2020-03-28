<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 */
class DwzLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dwz_logs';
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
    protected $casts = [];

    protected $connection = 'default';

    const CREATED_AT = 'create_time';

    const UPDATED_AT = 'update_time';

}