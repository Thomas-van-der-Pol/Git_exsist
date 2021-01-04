<?php

namespace App\Models\Consumer\Document;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use KJLocalization;

class Contact extends Authenticatable implements CanResetPassword
{
    use Notifiable;

    protected $guard = 'document';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CRM_CONTACT';

    protected $primaryKey = 'ID';

    public function getAuthIdentifier() {
        return $this->ID;
    }

    protected $guarded = ['ID'];

    /*
    * Override van standaard username field
    */
    public function username() {
        return 'EMAILADDRESS';
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'REMEMBER_TOKEN', 'PASSWORD', 'DOCUMENT_PASSWORD', 'DOCUMENT_REMEMBER_TOKEN'
    ];

    /**
     * Zorgt er voor dat laravel standaard niet de velden updated_at en created_at gaat zetten bij aanmaken nieuwe (wij hebben eigen LMTs hiervoor)
     */
    public $timestamps = false;

    public function getTitleAttribute()
    {
        $name = $this->FULLNAME;
        if (!$this->ACTIVE) {
            $name .= ' (' . strtolower(KJLocalization::translate('Algemeen', 'Inactief', 'Inactief')) . ')';
        }

        return $name;
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail() {
        return $this->EMAILADDRESS;
    }

    /**
     * Overriden omdat we password met hoofdletters in DB hebben dus afwijkend veld
     *
     * @return string
     */
    public function getAuthPassword() {
        return $this->DOCUMENT_PASSWORD;
    }

    /*
    * Override want veld heet anders (hoofdletters) in onze DB
    */
    public function setRememberToken($token) {
        $this->DOCUMENT_REMEMBER_TOKEN = $token;
    }

}