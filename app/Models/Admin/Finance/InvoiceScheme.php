<?php

namespace App\Models\Admin\Finance;

use App\Models\Admin\Assortment\Product;
use App\Models\Admin\Project\Project;
use Illuminate\Database\Eloquent\Model;
use KJ\Localization\libraries\LanguageUtils;
use KJLocalization;

class InvoiceScheme extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'FINANCE_INVOICE_SCHEME';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function product()
    {
        return $this->hasOne(Product::class, 'ID', 'FK_ASSORTMENT_PRODUCT');
    }

    public function project_product()
    {
        return $this->hasOne(\App\Models\Admin\Project\Product::class, 'ID', 'FK_PROJECT_ASSORTMENT_PRODUCT');
    }

    public function invoiceLine(){
        return $this->hasOne(InvoiceLine::class, 'ID', 'FK_FINANCE_INVOICE_LINE');
    }

    function getDateFormattedAttribute()
    {
        if ($this->DATE) {
            return date(LanguageUtils::getDateFormat(), strtotime($this->DATE));
        } else {
            return '';
        }
    }

    public function getPercentageDecimalAttribute()
    {
        if((integer)$this->PERCENTAGE <> $this->PERCENTAGE) {
            return number_format($this->PERCENTAGE, 2, '.', '.');
        } else {
            return number_format($this->PERCENTAGE);
        }
    }

    public function getPercentageFormattedAttribute()
    {
        if ($this->PERCENTAGE) {
            if ((integer)$this->PERCENTAGE <> $this->PERCENTAGE) {
                return number_format($this->PERCENTAGE, 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator()) . ' %';
            } else {
                return number_format($this->PERCENTAGE) . ' %';
            }
        } else {
            return '';
        }
    }

    public function getPricePercentageFormattedAttribute()
    {

        if ($this->PERCENTAGE && $this->project_product) {
            return '€ ' . number_format($this->product->PRICE * ( $this->PERCENTAGE / 100),2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
        } else {
            return '';
        }
    }
    public function getPriceMultipleAmountFormattedAttribute()
    {
        if ($this->project_product) {
            return '€ ' . number_format($this->project_product->PRICE * $this->project_product->QUANTITY,2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
        } else {
            return '';
        }
    }

    public function getPricePercentageMultipleAmountFormattedAttribute()
    {
        if ($this->project_product) {
            return '€ ' . number_format(($this->project_product->PRICE * $this->project_product->QUANTITY) * ( $this->PERCENTAGE / 100),2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
        } else {
            return '';
        }
    }

    public function getPricePercentageFormattedAttributeFloat()
    {

        if ($this->PERCENTAGE && $this->project_product) {
            return number_format($this->product->PRICE * ( $this->PERCENTAGE / 100),2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
        } else {
            return '';
        }
    }

}