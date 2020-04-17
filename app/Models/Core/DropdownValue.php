<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class DropdownValue extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_DROPDOWNVALUE';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /* Current value in active language */
    public function getValueAttribute($forceLocaleId = 0)
    {
        return Translation::getValue($this->TL_VALUE, $forceLocaleId);
    }

}