<?php

namespace App\Models\Admin\Country;

use App\Models\Admin\School\School;
use App\Models\Core\ContentItem;
use App\Models\Core\Translation;
use Illuminate\Database\Eloquent\Model;

class Country extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_COUNTRY';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function cities()
    {
        return $this->hasMany(City::class, 'FK_CORE_COUNTRY', 'ID');
    }

    public function schools()
    {
        return $this->hasMany(School::class, 'FK_CORE_COUNTRY', 'ID');
    }

    public function contentItems()
    {
        return ContentItem::where('FK_TABLE', $this->getTable())->where('FK_ITEM', $this->ID)->orderBy('SEQUENCE');
    }

    /* Current value in active language */
    public function getCountryNameAttribute($forceLocaleId = 0)
    {
        return Translation::getValue($this->TL_COUNTRYNAME, $forceLocaleId);
    }

}