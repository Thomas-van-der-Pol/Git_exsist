<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class WorkflowState extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_WORKFLOWSTATE';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

}