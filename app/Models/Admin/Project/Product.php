<?php

namespace App\Models\Admin\Project;

use App\Models\Admin\CRM\Relation;
use App\Models\Admin\Finance\Invoice;
use App\Models\Admin\Finance\InvoiceScheme;
use App\Models\Admin\Task\Task;
use App\Models\Core\DropdownValue;
use Illuminate\Database\Eloquent\Model;
use KJ\Localization\libraries\LanguageUtils;

class Product extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'PROJECT_ASSORTMENT_PRODUCT';
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
        return $this->hasOne(\App\Models\Admin\Assortment\Product::class, 'ID', 'FK_ASSORTMENT_PRODUCT');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'ID', 'FK_PROJECT');
    }

    public function relation()
    {
        return $this->hasOne(Relation::class, 'ID', 'FK_CRM_RELATION');
    }

    public function hasInvoices()
    {
        return ($this->invoices->where('ACTIVE', true)->count() > 0);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'FK_PROJECT_ASSORTMENT_PRODUCT ', 'ID');
    }

    public function invoiceSchemes()
    {
        return $this->hasMany(InvoiceScheme::class, 'FK_PROJECT_ASSORTMENT_PRODUCT', 'ID');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'FK_PROJECT_ASSORTMENT_PRODUCT', 'ID');
    }

    public function getPriceFormattedAttribute()
    {
        if ($this->product) {
            return '€ ' . number_format($this->PRICE,2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
        } else {
            return '';
        }
    }

    public function getTotalPriceFormattedAttribute()
    {
        if ($this->product) {

            $quantity = ($this->QUANTITY ?? 1);

            return '€ ' . number_format($this->PRICE * $quantity,2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
        } else {
            return '';
        }
    }

    public function checkRemnant()
    {
        $this->load('invoiceSchemes');
        $total = $this->invoiceSchemes->where('AUTOMATIC_REMNANT', false)->sum('PERCENTAGE');
        $remnant = (100 - $total);

        $invoiceScheme = InvoiceScheme::firstOrCreate([
            'FK_PROJECT_ASSORTMENT_PRODUCT' => $this->ID,
            'FK_ASSORTMENT_PRODUCT' => $this->product->ID,
            'ACTIVE' => true,
            'AUTOMATIC_REMNANT' => true
        ], [
            'DATE' => date('Y-m-d')
        ]);

        $invoiceScheme->PERCENTAGE = $remnant;
        $invoiceScheme->save();

        // Delete if remnant = 0
        if ($invoiceScheme->PERCENTAGE <= 0) {
            $invoiceScheme->delete();
        }
    }

    public function editable(){
        $allInvoiceSchemes = $this->invoiceSchemes->whereNotNull('FK_FINANCE_INVOICE_LINE')->count();
        return ($allInvoiceSchemes > 0) ? false : true;
    }

    public function getCompensationPercentageDecimalAttribute()
    {
        if ($this->COMPENSATION_PERCENTAGE) {
            return number_format($this->COMPENSATION_PERCENTAGE,2, '.', '.');
        } else {
            return '';
        }
    }

    public function getCompensationPercentageFormattedAttribute()
    {
        if ($this->COMPENSATION_PERCENTAGE) {
            return number_format($this->COMPENSATION_PERCENTAGE,2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator()) . '%';
        } else {
            return '';
        }
    }

}