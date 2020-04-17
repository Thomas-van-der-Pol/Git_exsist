<?php

namespace App\Models\Admin\Finance;

use Illuminate\Database\Eloquent\Model;
use KJ\Localization\libraries\LanguageUtils;

class VAT extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'FINANCE_VAT';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function getPercentageFormattedAttribute()
    {
        if ($this->PERCENTAGE) {
            return number_format($this->PERCENTAGE,0, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator()) . ' %';
        } else {
            return '';
        }
    }
}