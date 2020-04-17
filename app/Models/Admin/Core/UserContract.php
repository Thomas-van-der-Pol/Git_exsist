<?php

namespace App\Models\Admin\Core;

use App\Models\Admin\User;
use App\Models\Core\DropdownValue;
use Illuminate\Database\Eloquent\Model;
use KJ\Localization\libraries\LanguageUtils;

class UserContract extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_USER_CONTRACT';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function getDateStartFormattedAttribute()
    {
        return ($this->DATE_START == NULL ? '' : date(LanguageUtils::getDateFormat(), strtotime($this->DATE_START)));
    }

    public function getDateEndFormattedAttribute()
    {
        return ($this->DATE_END == NULL ? '' : date(LanguageUtils::getDateFormat(), strtotime($this->DATE_END)));
    }

    public function getDateProbationFormattedAttribute()
    {
        return ($this->DATE_PROBATION == NULL ? '' : date(LanguageUtils::getDateFormat(), strtotime($this->DATE_PROBATION)));
    }

    public function contracttype()
    {
        return $this->hasOne(DropdownValue::class, 'ID', 'FK_CORE_DROPDOWNVALUE_USERCONTRACTTYPE');
    }

    public function days()
    {
        return $this->hasMany(UserContractDay::class, 'FK_CORE_USER_CONTRACT', 'ID');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'ID', 'FK_CORE_USER');
    }

}