<?php

namespace App\Models\Core;

use App\Models\Core\Translation;
use Illuminate\Database\Eloquent\Model;

class ContentItem extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CONTENT_ITEM';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function getTitleAttribute($forceLocaleId = 0)
    {
        return Translation::getValue($this->TL_TITLE, $forceLocaleId);
    }

    public function getContentAttribute($forceLocaleId = 0)
    {
        return Translation::getValue($this->TL_CONTENT, $forceLocaleId);
    }

}