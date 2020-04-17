<?php

namespace App\Models\Admin\CRM;

use App\Models\Core\DropdownValue;
use App\Notifications\Consumer\Auth\WelcomePassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\Consumer\Auth\ResetPassword as ResetPasswordNotification;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Password;
use KJLocalization;

class Contact extends Authenticatable implements CanResetPassword
{
    use Notifiable;

    protected $guard = 'web';

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
        return config('app.test_email_mode') ? config('app.test_email') : $this->EMAILADDRESS;
    }

    /**
     * Overriden omdat we password met hoofdletters in DB hebben dus afwijkend veld
     *
     * @return string
     */
    public function getAuthPassword() {
        return $this->PASSWORD;
    }

    /**
     * Overriden omdat we username afwijkend hebben gemaakt voor wachtwoord rest
     *
     * @return string
     */
    public function getEmailForPasswordReset() {
        return $this->EMAILADDRESS;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token) {
        $this->notify(new ResetPasswordNotification($token));
    }

    /*
    * Override want veld heet anders (hoofdletters) in onze DB
    */
    public function setRememberToken($token) {
        $this->REMEMBER_TOKEN = $token;
    }

    public static function generatePassword($encrypt = true, $length = 35) {
        // Generate random string
        $pw = str_random($length);

        // Encrypt it
        if ($encrypt == true) {
            $pw = bcrypt($pw);
        }

        return $pw;
    }

    public function relation()
    {
        return $this->hasOne(Relation::class, 'ID', 'FK_CRM_RELATION');
    }

    public function gender()
    {
        return $this->hasOne(DropdownValue::class, 'ID', 'FK_CORE_DROPDOWNVALUE_GENDER');
    }

    public static function isEmailValid(int $id, string $email)
    {
        $contacts = Contact::where('EMAILADDRESS', $email)
            ->where('ACTIVE', true);

        if ($id != -1) {
            $contacts->where('ID', '<>', $id);
        }

        return ($contacts->count() == 0);
    }

    public function sendWelcomeEmail()
    {
        // Generate a new reset password token
        $token = Password::broker('users')->createToken($this);
        $this->notify(new WelcomePassword($token));
    }
}