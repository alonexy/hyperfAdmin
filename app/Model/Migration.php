<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property string $migration 
 * @property int $batch 
 */
class Migration extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'migrations';
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
    protected $casts = ['id' => 'int', 'batch' => 'integer'];
}