<?php

namespace App\Models\Admin\Task;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Task\Task;
use KJLocalization;

class TaskList extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'STANDARD_TASK_LIST';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function tasks()
    {
        return $this->hasMany(Task::class, 'FK_TASK_LIST', 'ID');
    }

}