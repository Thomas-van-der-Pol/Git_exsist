<?php

namespace App\Models\Admin\Finance;

use Illuminate\Database\Eloquent\Model;
use KJLocalization;

class InvoiceSection extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'FINANCE_INVOICE_SECTION';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

}