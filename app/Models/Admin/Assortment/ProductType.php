<?php

namespace App\Models\Admin\Assortment;

use Illuminate\Database\Eloquent\Model;
use KJLocalization;

class ProductType extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ASSORTMENT_PRODUCT_TYPE';
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
}