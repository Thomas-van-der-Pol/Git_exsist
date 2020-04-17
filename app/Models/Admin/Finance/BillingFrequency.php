<?php

namespace App\Models\Admin\Finance;

use Illuminate\Database\Eloquent\Model;
use KJLocalization;

class BillingFrequency extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'FINANCE_BILLINGFREQUENCY';
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