<?php

namespace App\Models\Admin\Finance;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'FINANCE_LEDGER';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

}