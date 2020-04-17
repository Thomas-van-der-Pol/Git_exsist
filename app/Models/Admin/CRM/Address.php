<?php

namespace App\Models\Admin\CRM;

use App\Models\Core\DropdownValue;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CRM_RELATION_ADDRESS';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function address()
    {
        return $this->hasOne(\App\Models\Admin\Core\Address::class, 'ID', 'FK_CORE_ADDRESS');
    }

    public function addressType()
    {
        return $this->hasOne(DropdownValue::class, 'ID', 'FK_CORE_DROPDOWNVALUE_ADRESSTYPE');
    }

    public function getFullAddressAttribute()
    {
        return $this->address->fullAddress();
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