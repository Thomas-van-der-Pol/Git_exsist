<?php

namespace App\Models\Admin\Task;

use App\Models\Admin\CustomMap;
use Illuminate\Database\Eloquent\Model;
use KJLocalization;

class TaskCustomMap extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'TASK_USER_CUSTOM_MAP';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function task()
    {
        return $this->hasOne(Task::class, 'ID', 'FK_TASK');
    }

    public function tasks()
    {
        return $this->hasOne(CustomMap::class, 'ID', 'FK_USER_CUSTOM_MAP');

    }

}