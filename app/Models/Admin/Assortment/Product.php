<?php

namespace App\Models\Admin\Assortment;

use App\Models\Core\Document;
use App\Models\Core\DropdownValue;
use Illuminate\Database\Eloquent\Model;
use KJ\Localization\libraries\LanguageUtils;
use KJLocalization;

class Product extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ASSORTMENT_PRODUCT';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $appends = ['PriceDecimal'];

    public function getTitleAttribute()
    {
        $name = $this->DESCRIPTION_INT;
        if (!$this->ACTIVE) {
            $name .= ' (' . strtolower(KJLocalization::translate('Algemeen', 'Inactief', 'Inactief')) . ')';
        }

        return $name;
    }

    public function producttype()
    {
        return $this->hasOne(ProductType::class, 'ID', 'FK_ASSORTMENT_PRODUCT_TYPE');
    }

    public function document()
    {
        return $this->hasOne(Document::class, 'ID', 'FK_DOCUMENT');
    }

    public function getPriceDecimalAttribute()
    {
        if ($this->PRICE) {
            return number_format($this->PRICE,2, '.', '.');
        } else {
            return '';
        }
    }

    public function getPriceFormattedAttribute()
    {
        if ($this->PRICE) {
            return '€ ' . number_format($this->PRICE,2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
        } else {
            return '';
        }
    }

    public function getPriceIncFormattedAttribute()
    {
        if ($this->PRICE_INCVAT) {
            return '€ ' . number_format($this->PRICE_INCVAT,2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
        } else {
            return '';
        }
    }

}