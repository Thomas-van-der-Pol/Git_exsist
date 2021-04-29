<?php namespace App\Console\Commands\User;

use App\Models\Admin\Core\Notification;
use App\Models\Admin\Core\Role;
use App\Models\Admin\User;
use App\Models\Core\Setting\SettingValue;
use Illuminate\Console\Command;
use KJLocalization;

class BirthdayNotification extends Command {

    protected $name = 'exsist:birthday_notifications';

    protected $description = 'Automatically notify about birthdays: test';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $daysBeforeBirthdayNotification = (int)SettingValue::getValue(config('setting.AANTAL_DAGEN_VERJAARDAG_NOTIFICATIE'));

        if ($daysBeforeBirthdayNotification > 0)
        {
            $birthdayUsers = User::whereRaw("CAST(DATEADD(DAY, -".$daysBeforeBirthdayNotification.", DATETIMEFROMPARTS(YEAR(GETDATE()), DATEPART(M, DATE_OF_BIRTH), DATEPART(DAY, DATE_OF_BIRTH), 0,0,0,0)) AS DATE) = CAST(GETDATE() AS DATE)")
                ->where([
                    'ACTIVE' => true,
                    'RECEIVE_NOTIFICATION' => true
                ])
                ->whereNotNull('FK_CORE_ROLE_NOTIFICATION')
                ->get();

            foreach ($birthdayUsers as $birthdayUser)
            {
                $role = Role::find($birthdayUser->FK_CORE_ROLE_NOTIFICATION);
                if ($role) {
                    $users = $role->userRoles;
                    foreach ($users as $user) {
                        $notification = new Notification([
                            'RECIPIENT_FK_CORE_USER' => $user->FK_CORE_USER,
                            'DATE' => date('Y-m-d'),
                            'SUBJECT' => KJLocalization::translate('Admin - Werknemers', 'Verjaardag notificatie titel', 'De verjaardag van :USER is over :DAGEN dagen', [
                                'USER' => $birthdayUser->title,
                                'DAGEN' => $daysBeforeBirthdayNotification
                            ]),
                            'CONTENT' => KJLocalization::translate('Admin - Werknemers', 'Verjaardag notificatie tekst', 'De verjaardag van :USER is op :DATE', [
                                'USER' => $birthdayUser->title,
                                'DATE' => date('d-m', strtotime($birthdayUser->DATE_OF_BIRTH))
                            ]),
                            'SOURCE_TABLE' => $birthdayUser->getTable(),
                            'SOURCE_ID' => $birthdayUser->ID,
                            'SOURCE_URL' => '/admin/settings/user/detail/' . $birthdayUser->ID,
                            'READED' => false
                        ]);
                        $notification->save();
                    }
                }
            }
        }
        else
        {
            echo "Setting 'AANTAL_DAGEN_VERJAARDAG_NOTIFICATIE' not set \r\n";
        }
    }

}