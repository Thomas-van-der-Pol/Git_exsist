<?php

namespace App\Models\Admin\Core;

use App\Models\Admin\User;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_USER_ROLE';
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

    public function role()
    {
        return $this->hasOne(Role::class, 'ID', 'FK_CORE_ROLE');
    }

}