<?php

namespace App\Models\Admin\Core;

use App\Models\Core\Translation;
use Illuminate\Database\Eloquent\Model;

class Language extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_LANGUAGE';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /* Current value in active language */
    public function getLanguageDescriptionAttribute($forceLocaleId = 0)
    {
        return Translation::getValue($this->TL_DESCRIPTION, $forceLocaleId);
    }

}