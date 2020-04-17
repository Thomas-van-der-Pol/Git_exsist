<?php

namespace App\Models\Admin\Core;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_ROLE_PERMISSION';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

}