<?php

namespace App\Models\Admin\Finance;


use App\Models\Admin\Project\Product;
use App\Models\Admin\Project\Project;
use Illuminate\Database\Eloquent\Model;
use KJ\Localization\libraries\LanguageUtils;
use KJLocalization;

class InvoiceLine extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'FINANCE_INVOICE_LINE';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function(InvoiceLine $invoiceLine) {

            // Unlink from invoice scheme
            if ($invoiceLine->FK_FINANCE_INVOICE_SCHEME) {
                $item = InvoiceScheme::find($invoiceLine->FK_FINANCE_INVOICE_SCHEME);
                $item->FK_FINANCE_INVOICE_LINE = null;
                $item->save();
            }

        });
    }

    public function invoiceScheme(){
        return $this->hasOne(InvoiceScheme::class, 'FK_FINANCE_INVOICE_LINE', 'ID');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'ID', 'FK_FINANCE_INVOICE');
    }

    public function vat()
    {
        return $this->hasOne(VAT::class, 'ID', 'FK_FINANCE_VAT');
    }

    public function ledger()
    {
        return $this->hasOne(Ledger::class, 'ID', 'FK_FINANCE_LEDGER');
    }

    public function getQuantityDecimalAttribute()
    {
        if((integer)$this->QUANTITY <> $this->QUANTITY) {
            return number_format($this->QUANTITY, 2, '.', '.');
        } else {
            return number_format($this->QUANTITY);
        }
    }

    public function getQuantityFormattedAttribute()
    {
        if((integer)$this->QUANTITY <> $this->QUANTITY) {
            return number_format($this->QUANTITY, 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
        } else {
            return number_format($this->QUANTITY);
        }
    }

    public function getPriceDecimalAttribute()
    {
        return number_format(($this->PRICE), 2, '.', '.');
    }

    public function getPriceFormattedAttribute()
    {
        return '€ ' . number_format(($this->PRICE), 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
    }

    public function getPriceIncVatFormattedAttribute()
    {
        return '€ ' . number_format(($this->PRICE_INCVAT), 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
    }

    public function getPriceTotalFormattedAttribute()
    {
        return '€ ' . number_format(($this->PRICE_TOTAL), 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
    }

    public function getPriceTotalIncVatFormattedAttribute()
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