<?php

namespace App\Models\Core\Setting;

use Illuminate\Database\Eloquent\Model;

class SettingGroup extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_SETTING_GROUP';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function settings()
    {
        return $this->hasMany(Setting::class, 'FK_CORE_SETTING_GROUP', 'ID')
            ->where('ACTIVE', true)
            ->orderBy('SEQUENCE');
    }

}