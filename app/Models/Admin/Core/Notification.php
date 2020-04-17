<?php

namespace App\Models\Admin\Core;

use App\Models\Admin\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use KJLocalization;

class Notification extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'NOTIFICATION';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function user()
    {
        return $this->hasOne(User::class, 'ID', 'RECIPIENT_FK_CORE_USER');
    }

    public function getDateFormattedAttribute()
    {
        if($this->DATE) {
            $date = new Carbon($this->DATE);
            $now = Carbon::now();
            $difference = ($date->diff($now)->days < 1)
                ? KJLocalization::translate('Admin - Taken', 'Vandaag', 'vandaag')
                : $date->diffForHumans($now);

            return $difference;
        } else {
            return KJLocalization::translate('Admin - Taken', 'Geen datum', 'Geen datum');
        }
    }
}