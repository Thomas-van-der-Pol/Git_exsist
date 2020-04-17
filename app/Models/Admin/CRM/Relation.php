<?php

namespace App\Models\Admin\CRM;

use App\Models\Admin\Core\Label;
use App\Models\Core\DropdownValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use KJ\Localization\libraries\LanguageUtils;
use KJLocalization;

class Relation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CRM_RELATION';
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
        $name = $this->NAME;
        if (!$this->ACTIVE) {
            $name .= ' (' . strtolower(KJLocalization::translate('Algemeen', 'Inactief', 'Inactief')) . ')';
        }

        return $name;
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'FK_CRM_RELATION', 'ID');
    }

    public function type()
    {
        return $this->hasOne(DropdownValue::class, 'ID', 'FK_CORE_DROPDOWNVALUE_RELATIONTYPE');
    }

    public function label()
    {
        return $this->hasOne(Label::class, 'ID', 'FK_CORE_LABEL');
    }

    public function generateDebtornumber()
    {
        if ($this->NUMBER_DEBTOR == '')
        {
            $this->NUMBER_DEBTOR = $this->label->getNewDebtorNumber();
            $this->save();
        }
    }

    public function createProduct($product)
    {
        foreach ($product as $product_id) {
            $productRelation = new Product([
                'ACTIVE' => true,
                'FK_CRM_RELATION' => $this->ID,
                'FK_ASSORTMENT_PRODUCT' => $product_id
            ]);
            $productRelation->save();
        }

        return $productRelation;
    }



    public function createService($service)
    {
        foreach ($service as $service_id) {
            $serviceUser = new Guideline([
                'ACTIVE' => true,
                'FK_CRM_RELATION' => $this->ID,
                'FK_ASSORTMENT_PRODUCT' => $service_id
            ]);
            $serviceUser->save();
        }

        return $serviceUser;
    }

    public function getRateKmFormattedAttribute()
    {
        if ($this->RATE_KM) {
            return '€ ' . number_format($this->RATE_KM,2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
        } else {
            return '';
        }
    }
}