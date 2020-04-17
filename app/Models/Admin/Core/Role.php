<?php

namespace App\Models\Admin\Core;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_ROLE';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'CORE_ROLE_PERMISSION', 'FK_CORE_ROLE', 'FK_CORE_PERMISSION');
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'FK_CORE_ROLE', 'ID');
    }

}