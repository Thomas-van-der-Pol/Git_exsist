<?php

namespace App\Models\Admin\Core;

use App\Models\Admin\Country\Country;
use App\Models\Core\DropdownValue;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_ADDRESS';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function country()
    {
        return $this->hasOne(Country::class, 'ID', 'FK_CORE_COUNTRY');
    }

    public function addressType()
    {
        return $this->hasOne(DropdownValue::class, 'ID', 'FK_CORE_DROPDOWNVALUE_ADRESSTYPE');
    }

    public function fullAddress($breaks = TRUE)
    {
        $address = $this->ADDRESSLINE . " " . $this->HOUSENUMBER . ($breaks ? "\n" : ' ');
        $address .= isset($this->ZIPCODE) ? $this->ZIPCODE . " " : "";
        $address .= isset($this->CITY) ? $this->CITY . ($breaks ? "\n" : ' ') : " ";
        $address .= isset($this->country) ? $this->country->getCountryNameAttribute() : "";

        return $address;
    }

    public function duplicate()
    {
        // Reset relations on existing model
        $this->relations = [];

        // Copy attributes
        $new = $this->replicate([
            'TS_CREATED',
            'TS_LASTMODIFIED'
        ]);

        // Save model
        $new->push();

        // Return new model
        return $new;
    }
}