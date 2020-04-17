<?php

namespace App\Models\Admin\Task;

use Illuminate\Database\Eloquent\Model;

class Filter extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'TASK_FILTER';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function dropdownvalue()
    {
        return $this->hasOne('App\Models\Core\DropdownValue', 'ID', 'FK_CORE_DROPDOWNVALUE');
    }
}