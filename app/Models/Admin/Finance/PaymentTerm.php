<?php

namespace App\Models\Admin\Finance;

use Illuminate\Database\Eloquent\Model;

class PaymentTerm extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'FINANCE_PAYMENT_TERM';

    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public static function isDefaultValid(int $id)
    {
        $paymentTerms = PaymentTerm::where('DEFAULT', true)->where('ACTIVE', true);

        if ($id != -1) {
            $paymentTerms->where('ID', '<>', $id);
        }

        $paymentTerm = $paymentTerms->first();

        return (($paymentTerm == null) || (($paymentTerm->DEFAULT == 0)));
    }
}