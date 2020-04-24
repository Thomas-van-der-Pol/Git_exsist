<?php

namespace App\Models\Core\Setting;

use Illuminate\Database\Eloquent\Model;

class SettingValue extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_SETTING_VALUE';
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

    public static function getValue($id)
    {
        $obj = static::where(['FK_CORE_SETTING' => $id])->first();
        return ($obj ? ($obj->VALUE_UNCONSTRAINED ?? '') : '');
    }

    public static function setValue($id, $value)
    {
        $obj = static::firstOrCreate([
            'FK_CORE_SETTING' => $id
        ]);
        $obj->VALUE_UNCONSTRAINED = $value;
        $obj->save();

        return $obj;
    }

}