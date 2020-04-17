<?php

namespace App\Models\Core\Setting;

use Illuminate\Database\Eloquent\Model;

class SettingAllowedValue extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_SETTING_ALLOWED_VALUE';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function setting()
    {
        return $this->hasOne(Setting::class, 'ID', 'FK_CORE_SETTING');
    }
}