<?php

namespace App\Models\Admin\Task;

use App\Models\Admin\User;
use Illuminate\Database\Eloquent\Model;
use KJ\Localization\libraries\LanguageUtils;

class TaskSubsription extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'TASK_SUBSCRIPTION';
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

    public function user()
    {
        return $this->hasOne(User::class, 'ID', 'FK_CORE_USER');
    }
}