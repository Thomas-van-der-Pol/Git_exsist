<?php

namespace App\Models\Admin\CRM;

use App\Models\Core\Translation;
use Illuminate\Database\Eloquent\Model;

class Contracttype extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CRM_CLIENT_CONTRACTTYPE';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /* Current value in active language */
    public function getContracttypeDescriptionAttribute($forceLocaleId = 0)
    {
        return Translation::getValue($this->TL_VALUE, $forceLocaleId);
    }
}