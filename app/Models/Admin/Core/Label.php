<?php

namespace App\Models\Admin\Core;

use App\Models\Core\Document;
use Illuminate\Database\Eloquent\Model;
use KJ\Localization\libraries\LanguageUtils;
use KJLocalization;

class Label extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_LABEL';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function getTitleAttribute()
    {
        $name = $this->DESCRIPTION;
        if (!$this->ACTIVE) {
            $name .= ' (' . strtolower(KJLocalization::translate('Algemeen', 'Inactief', 'Inactief')) . ')';
        }

        return $name;
    }

    public function getNewInvoiceNumber()
    {
        $number = (int)$this->NEXT_INVOICE_NUMBER;

        $this->NEXT_INVOICE_NUMBER = $number + 1;
        $this->save();

        return $number;
    }

    public function getNewDebtorNumber()
    {
        $number = (int)$this->NEXT_DEBTOR_NUMBER;

        $this->NEXT_DEBTOR_NUMBER = $number + 1;
        $this->save();

        return $number;
    }

    public function document()
    {
        return $this->hasOne(Document::class, 'ID', 'FK_DOCUMENT_PDF_PAPER');
    }

    public function getDefaultRateKmFormattedAttribute()
    {
        if ($this->DEFAULT_RATE_KM) {
            return '€ ' . number_format($this->DEFAULT_RATE_KM,2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
        } else {
            return '';
        }
    }
}