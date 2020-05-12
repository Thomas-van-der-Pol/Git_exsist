<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use KJ\Localization\libraries\LanguageUtils;

class DropdownValue extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_DROPDOWNVALUE';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function translations()
    {
        return $this->hasMany(Translation::class, 'FK_CORE_TRANSLATION_KEY', 'TL_VALUE');
    }

    /* Current value in active language */
    public function getValueAttribute($forceLocaleId = 0)
    {
        return Translation::getValue($this->TL_VALUE, $forceLocaleId);
    }

    public function getSequenceFormattedAttribute()
    {
        if((integer)$this->SEQUENCE <> $this->SEQUENCE) {
            return number_format($this->SEQUENCE, 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
        } else {
            return number_format($this->SEQUENCE);
        }
    }

}