<?php

namespace App\Models\Admin;

use App\Models\Admin\Task\Task;
use Illuminate\Database\Eloquent\Model;
use KJLocalization;

class CustomMap extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'USER_CUSTOM_MAP';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function user()
    {
        return $this->hasOne(User::class, 'ID', 'FK_CORE_USER');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'TASK_USER_CUSTOM_MAP', 'FK_USER_CUSTOM_MAP', 'FK_TASK');

    }

}