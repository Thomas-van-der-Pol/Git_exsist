<?php

namespace App\Models\Admin\Finance;

use App\Models\Admin\CRM\Relation;
use App\Models\Admin\Project\Project;
use Illuminate\Database\Eloquent\Model;
use KJ\Localization\libraries\LanguageUtils;
use KJLocalization;

class Billcheck extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'FINANCE_BILLCHECK';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function relation()
    {
        return $this->hasOne(Relation::class, 'ID', 'FK_CRM_RELATION');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'ID', 'FK_PROJECT');
    }

    public function getQuantityFormattedAttribute()
    {
        if((integer)$this->QUANTITY <> $this->QUANTITY) {
            $val = (string)floatval($this->QUANTITY);
            return str_replace('.', LanguageUtils::getDecimalPoint(),(string)$val);
        } else {
            return number_format($this->QUANTITY);
        }
    }

    public function getPriceFormattedAttribute()
    {
        return '€ ' . number_format(($this->PRICE), 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
    }

    public function getPriceTotalFormattedAttribute()
    {
        return '€ ' . number_format(($this->PRICE_TOTAL), 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
    }

    public function getPriceInclFormattedAttribute()
    {
        return '€ ' . number_format(($this->PRICE_INCVAT), 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
    }

    public function getPriceTotalInclFormattedAttribute()
    {
        return '€ ' . number_format(($this->PRICE_TOTAL_INCVAT), 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
    }

    public function getPeriodFormattedAttribute()
    {
        if(isset($this->STARTDATE)) {
            return date(LanguageUtils::getDateFormat(), strtotime($this->STARTDATE)). ' / '.date(LanguageUtils::getDateFormat(), strtotime($this->ENDDATE)).' ('.rtrim(rtrim($this->QUANTITY_MONTH,'0'),'.').KJLocalization::translate('Admin - Facturen', 'mnd', 'mnd').')';
        }
        else {
            return KJLocalization::translate('Admin - Facturen', 'Eenmalig', 'Eenmalig');
        }
    }

}