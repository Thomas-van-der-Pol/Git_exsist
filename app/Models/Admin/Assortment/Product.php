<?php

namespace App\Models\Admin\Assortment;

use App\Models\Admin\Finance\InvoiceScheme;
use App\Models\Admin\Task\Task;
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

    public function invoiceSchemes()
    {
        return $this->hasMany(InvoiceScheme::class, 'FK_ASSORTMENT_PRODUCT', 'ID');
    }
    public function tasks()
    {
        return $this->hasMany(Task::class, 'FK_ASSORTMENT_PRODUCT', 'ID');
    }

    public function checkRemnant()
    {
        $this->load('invoiceSchemes');
        $total = $this->invoiceSchemes->where('FK_PROJECT_ASSORTMENT_PRODUCT',null)->where('AUTOMATIC_REMNANT', false)->sum('PERCENTAGE');
        $remnant = (100 - $total);

        $invoiceScheme = InvoiceScheme::firstOrCreate([
            'FK_ASSORTMENT_PRODUCT' => $this->ID,
            'FK_PROJECT_ASSORTMENT_PRODUCT' => null,
            'ACTIVE' => true,
            'AUTOMATIC_REMNANT' => true
        ], [
            'DAYS' => 0
        ]);
        $invoiceScheme->PERCENTAGE = $remnant;
        $invoiceScheme->save();

        // Delete if remnant = 0
        if ($invoiceScheme->PERCENTAGE <= 0) {
            $invoiceScheme->delete();
        }
    }

    public function document()
    {
        return $this->hasOne(Document::class, 'ID', 'FK_DOCUMENT');
    }

    public function getPriceDecimalAttribute()
    {
        if ($this->PRICE) {
            return number_format($this->PRICE, 2, '.', '');
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