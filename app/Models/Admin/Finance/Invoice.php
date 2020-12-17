<?php

namespace App\Models\Admin\Finance;

use App\Models\Admin\Core\Label;
use App\Models\Admin\CRM\Address;
use App\Models\Admin\CRM\Contact;
use App\Models\Admin\CRM\Relation;
use App\Models\Admin\Project\Product;
use App\Models\Admin\Project\Project;
use App\Models\Core\Document;
use App\Models\Core\WorkflowState;
use DateInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use KJ\Localization\libraries\LanguageUtils;
use DateTime;
use KJLocalization;

class Invoice extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'FINANCE_INVOICE';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function(Invoice $invoice) {
            foreach ($invoice->lines as $line)
            {
                $line->delete();
            }

            foreach ($invoice->sections as $section)
            {
                $section->delete();
            }
        });
    }

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function getTitleAttribute()
    {
        $name = $this->relation->title;
        $name .= ' - ' . KJLocalization::translate('Admin - Facturen', 'Factuur', 'Factuur') . ' ' . ($this->NUMBER ?? KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept'));

        if (!$this->ACTIVE) {
            $name .= ' (' . strtolower(KJLocalization::translate('Algemeen', 'Inactief', 'Inactief')) . ')';
        }

        return $name;
    }

    public function generateNumber()
    {
        if ($this->NUMBER == '')
        {
            $this->NUMBER = $this->label->getNewInvoiceNumber();

            if (!isset($this->relation->NUMBER_DEBTOR) || $this->relation->NUMBER_DEBTOR == '') {
                $this->relation->generateDebtornumber();
            }

            $this->FK_CORE_WORKFLOWSTATE = config('workflowstate.INVOICE_FINAL');

            if(!isset($this->DATE)) {
                $this->DATE = new DateTime();
            }

            if(!isset($this->EXPIRATION_DATE)) {
                $expirationDate = new DateTime();
                $expirationDate->add(new DateInterval('P' . ((int) isset($this->relation->paymentTerm) ? $this->relation->paymentTerm->AMOUNT_DAYS : 0) . 'D'));
                $this->EXPIRATION_DATE = $expirationDate;
                $this->PAYMENT_TERM_CODE = ((int) isset($this->relation->paymentTerm) ? $this->relation->paymentTerm->CODE : 0);
                $this->FK_FINANCE_PAYMENT_TERM = ((int) isset($this->relation->paymentTerm) ? $this->relation->paymentTerm->ID : 0);
            }

            $this->save();
        }
    }

    public function addRemark($remark)
    {
        $currentDate = new DateTime();
        $this->REMARKS = ($this->REMARKS <> '' ? ($this->REMARKS . PHP_EOL) : '') . $currentDate->format(LanguageUtils::getDateTimeFormat())  . ' ' . Auth::guard()->user()->title . ': '.$remark;
        $this->save();
    }

    public function document()
    {
        return $this->hasOne(Document::class, 'ID', 'FK_DOCUMENT');
    }

    public function document_anonymized()
    {
        return $this->hasOne(Document::class, 'ID', 'FK_DOCUMENT_ANONYMIZED');
    }

    public function document_compensation_letter()
    {
        return $this->hasOne(Document::class, 'ID', 'FK_DOCUMENT_COMPENSATION_LETTER');
    }

    public function label()
    {
        return $this->hasOne(Label::class, 'ID', 'FK_CORE_LABEL');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'ID', 'FK_PROJECT');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'ID', 'FK_PROJECT_ASSORTMENT_PRODUCT');
    }

    public function relation()
    {
        return $this->hasOne(Relation::class, 'ID', 'FK_CRM_RELATION');
    }

    public function contact()
    {
        return $this->hasOne(Contact::class, 'ID', 'FK_CRM_CONTACT');
    }

    public function lines()
    {
        return $this->hasMany(InvoiceLine::class, 'FK_FINANCE_INVOICE', 'ID');
    }

    public function sections()
    {
        return $this->hasMany(InvoiceSection::class, 'FK_FINANCE_INVOICE', 'ID');
    }

    public function workflowstate()
    {
        return $this->hasOne(WorkflowState::class, 'ID', 'FK_CORE_WORKFLOWSTATE');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'ID', 'FK_CRM_RELATION_ADDRESS');
    }

    public function getDaysRemainingInt()
    {
        $today = new DateTime();
        $expirationDate = new DateTime($this->EXPIRATION_DATE);

        return $expirationDate->diff($today)->format("%R%a");
    }

    public function getDaysRemaining()
    {
        $today = new DateTime();
        $expirationDate = new DateTime($this->EXPIRATION_DATE);

        $daysLeft = $this->getDaysRemainingInt();

        // Vervaldatum in het verleden
        if ($expirationDate < $today) {
            return $daysLeft . ' ' . KJLocalization::translate('Admin - Facturen', 'dagen', 'dag(en)') . ' ' . KJLocalization::translate('Admin - Facturen', 'vervallen', 'vervallen');
        } else {
            return $daysLeft . ' ' . KJLocalization::translate('Admin - Facturen', 'dagen', 'dag(en)') . ' ' . KJLocalization::translate('Admin - Facturen', 'resterend', 'resterend');
        }
    }

    public function getDaysOpenAttribute()
    {
        $invoicedate = new DateTime($this->DATE);
        $today = new DateTime();

        return $invoicedate->diff($today)->format("%a");
    }

    public function getDateFormattedAttribute()
    {
        // Datum in juiste formaat
        return isset($this->NUMBER) ? (isset($this->DATE) ? date(LanguageUtils::getDateFormat(), strtotime($this->DATE)) : '') : '';
    }

    public function getExpirationDateFormattedAttribute()
    {
        // Datum in juiste formaat
        return isset($this->EXPIRATION_DATE) ? date(LanguageUtils::getDateFormat(), strtotime($this->EXPIRATION_DATE)) : '';
    }

    public function getPaidFormattedAttribute()
    {
        return ($this->PAID == true) ? KJLocalization::translate('Algemeen', 'Ja', 'Ja') : KJLocalization::translate('Algemeen', 'Nee', 'Nee');
    }

    public function getAdvanceFormattedAttribute()
    {
        return ($this->IS_ADVANCE == true) ? KJLocalization::translate('Algemeen', 'Ja', 'Ja') : KJLocalization::translate('Algemeen', 'Nee', 'Nee');
    }

    public function getTotalExclFormattedAttribute()
    {
        return '€ ' . number_format(($this->PRICE_TOTAL_EXCL), 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
    }

    public function getTotalVatFormattedAttribute()
    {
        return '€ ' . number_format(($this->VAT_TOTAL), 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
    }

    public function getTotalInclFormattedAttribute()
    {
        return '€ ' . number_format(($this->PRICE_TOTAL_INCL), 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
    }

    public function getCompensatedPriceFormattedAttribute()
    {
        return '€ ' . number_format($this->lines->where('IS_COMPENSATION', true)->sum('PRICE_TOTAL_INCVAT')*-1, 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
    }

}