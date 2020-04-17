<?php

namespace App\Models\Core\Setting;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_SETTING';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function group()
    {
        return $this->hasOne(SettingGroup::class, 'ID', 'FK_CORE_SETTING_GROUP');
    }

    public function allowedValue()
    {
        return $this->hasMany(SettingAllowedValue::class, 'FK_CORE_SETTING', 'ID')
            ->where('ACTIVE', true)
            ->orderBy('SEQUENCE');
    }

}