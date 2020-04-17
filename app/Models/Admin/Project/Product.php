<?php

namespace App\Models\Admin\Project;

use App\Models\Admin\CRM\Relation;
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

}