<?php

namespace App\Models\Admin;

use App\Models\Admin\Core\Address;
use App\Models\Admin\Core\Role;
use App\Notifications\Admin\Auth\WelcomePassword;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\Admin\Auth\ResetPassword as ResetPasswordNotification;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Password;
use KJ\Localization\libraries\LanguageUtils;
use KJLocalization;

class User extends Authenticatable implements CanResetPassword
{
    use Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_USER';

    //Nodig om deze afwijkend te zetten omdat Laravel standaard id in kleine letters pakt
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
        'REMEMBER_TOKEN', 'PASSWORD'
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

    public static function generatePassword() {
        // Generate random string and encrypt it.
        return bcrypt(str_random(35));
    }

    public static function isEmailValid(int $userId, string $email)
    {
        $users = User::where('EMAILADDRESS', $email)->where('ACTIVE', true);

        if ($userId != -1) {
            $users->where('ID', '<>', $userId);
        }

        return ($users->count() == 0);
    }

    public static function isUserCodeValid(int $userId, $userCode)
    {
        $users = User::where('USERCODE', $userCode)->where('ACTIVE', true);

        if ($userId != -1) {
            $users->where('ID', '<>', $userId);
        }

        return ($users->count() == 0);
    }

    public function sendWelcomeEmail()
    {
        // Generate a new reset password token
        $token = Password::broker('admins')->createToken($this);
        $this->notify(new WelcomePassword($token));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'CORE_USER_ROLE', 'FK_CORE_USER', 'FK_CORE_ROLE')->where('ACTIVE', true);
    }

    public function permissions()
    {
        $permissions = Collection::make();
        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }

        return $permissions;
    }

    public function hasPermission(int $id)
    {
        return $this->permissions()->where('ID', $id)->count() > 0;
    }

    public function getDateOfBirthFormattedAttribute()
    {
        if ($this->DATE_OF_BIRTH) {
            return date(LanguageUtils::getDateFormat(), strtotime($this->DATE_OF_BIRTH));
        } else {
            return '';
        }
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'ID', 'FK_CORE_ADDRESS');
    }


    public function initialsByName()
    {
        $nameElements = explode(" ", $this->FULLNAME);
        $result = "";

        foreach ($nameElements as $nameElement) {
            $result .= $nameElement[0];
        }

        return $result;
    }
}
