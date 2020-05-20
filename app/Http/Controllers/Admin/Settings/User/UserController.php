<?php

namespace App\Http\Controllers\Admin\Settings\User;

use App\Libraries\Core\DropdownvalueUtils;
use App\Models\Admin\Core\Address;
use App\Models\Admin\Core\Notification;
use App\Models\Admin\Core\Role;
use App\Models\Admin\Core\UserRole;
use App\Models\Admin\Country\Country;
use App\Models\Admin\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use KJ\Core\controllers\AdminBaseController;
use Illuminate\Http\Request;
use KJ\Core\libraries\SessionUtils;
use KJLocalization;

class UserController extends AdminBaseController
{
    protected $model = 'App\Models\Admin\User';

    protected $mainViewName = 'admin.settings.user.main';

    protected $allColumns = ['ID', 'FULLNAME','ACTIVE','EMAILADDRESS', 'FK_CORE_ADDRESS', 'PHONE_MOBILE', 'PHONE_EMERGENCY'];

    protected $datatableDefaultSort = array(
        [
            'field' => 'FULLNAME',
            'sort'  => 'ASC'
        ]
    );

    protected $detailScreenFolder = 'admin.settings.user.detail_screens';
    protected $detailViewName = 'admin.settings.user.detail';

    protected $saveUnsetValues = [
        'ROLES',
        'PHOTO',
        'ADDRESS_FK_CORE_COUNTRY',
        'ADDRESS_ZIPCODE',
        'ADDRESS_HOUSENUMBER',
        'ADDRESS_ADDRESSLINE',
        'ADDRESS_CITY',
        'ADDRESS_FULL'
    ];

    protected $saveValidation = [
        'EMAILADDRESS'          => 'nullable|email',
        'PHOTO'                 => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ];

    protected $exceptAuthorization = ['currentUser'];

    public function __construct()
    {
        parent::__construct();

        $this->saveValidationMessages = [
            'PHOTO.uploaded' => KJLocalization::translate('Algemeen', 'Afbeelding te groot', 'Kan afbeelding niet uploaden. De maximale bestandsgrootte is 2 MB.'),
            'PHOTO.max' => KJLocalization::translate('Algemeen', 'Afbeelding te groot', 'Kan afbeelding niet uploaden. De maximale bestandsgrootte is 2 MB.')
        ];
    }

    protected function authorizeRequest($method, $parameters)
    {
        return ( Auth::guard()->user()->hasPermission(config('permission.INSTELLINGEN_WERKNEMERS')) && Auth::guard()->user()->hasPermission(config('permission.INSTELLINGEN')));
    }

    protected function beforeIndex()
    {
        $status = DropdownvalueUtils::getStatusDropdown(false);

        $bindings = array(
            ['status', $status]
        );

        return $bindings;
    }

    public function allDatatable(Request $request)
    {
        $this->datatableFilter = array(
            ['ACTIVE', array(
                'param' => 'ACTIVE',
                'default' => SessionUtils::getSession('ADM_USER', 'ADM_FILTER_USER_STATUS', 1)
            )],
            ['FULLNAME, EMAILADDRESS', array(
                'param' => 'ADM_FILTER_USER',
                'operation' => 'like',
                'default' => SessionUtils::getSession('ADM_USER', 'ADM_FILTER_USER', '')
            )]
        );

        return parent::allDatatable($request);
    }

    public function beforeDetailScreen(int $id, $item, $screen)
    {
        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        $bindings = [];

        switch ($screen) {
            case 'default':
                $genders = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_GENDER'),true);
                $positions = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_POSITIONS'),true);
                $departments = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_DEPARTMENTS'),true);

                $countriesOri = Country::all()->where('ACTIVE', true)->sortBy('country_name')->pluck('country_name', 'ID');
                $countries = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'] + $countriesOri->toArray();

                $rolesOri = Role::where([
                    'ACTIVE' => true
                ])->orderBy('DESCRIPTION')->pluck('DESCRIPTION', 'ID');
                $roles = $none + $rolesOri->toArray();

                $bindings = array_merge($bindings, [
                    ['genders', $genders],
                    ['positions', $positions],
                    ['departments', $departments],
                    ['countries', $countries],
                    ['roles', $roles]
                ]);
            break;

            case 'permissions':
                $roles = Role::where('ACTIVE', true)->orderBy('DESCRIPTION')->get();
                $userroles = UserRole::where('FK_CORE_USER', $id)->pluck('FK_CORE_ROLE');

                $bindings = array_merge($bindings, [
                    ['roles', $roles],
                    ['userroles', $userroles]
                ]);
            break;
        }

        return $bindings;
    }

    public function save(Request $request)
    {
        // E-mailadres valideren of niet al/dubbel in systeem
        $userId = $request->get('ID');
        $email = ($request->get('EMAILADDRESS') ? $request->get('EMAILADDRESS') : '');
        $userCode = ($request->get('USERCODE') ? $request->get('USERCODE') : '');

        if ($userCode != '') {
            if (!User::isUserCodeValid($userId, $userCode)) {
                return response()->json([
                    'message' => KJLocalization::translate('Admin - Werknemers', 'Personeelsnummer in gebruik', 'Dit personeelsnummer is al in gebruik'). '!',
                    'success'=> false
                ], 200);
            }
        }

        if ($email != '') {
            if (!User::isEmailValid($userId, $email)) {
                return response()->json([
                    'message' => KJLocalization::translate('Admin - Werknemers', 'E-mailadres in gebruik', 'Dit e-mailadres is al in gebruik'). '!',
                    'success'=> false
                ], 200);
            }
        }

        return parent::save($request);
    }

    protected function afterSave($item, $originalItem, Request $request, &$response)
    {
        //1. Indien nieuwe user, dan welkom e-mail sturen
        if ( ($request->get('ID') == -1) && ($request->get('LOGIN_ENABLED'))) {
            $pw = User::generatePassword();
            $item->PASSWORD = $pw;
            $item->save();

            $item->sendWelcomeEmail();
        }

        //2 Rollen invoeren
        if ($request->get('ROLES') != null) {
            UserRole::where('FK_CORE_USER', $item->ID)->delete();
            $roles = $request->get('ROLES');
            if($roles) {
                foreach($roles as $roleID) {
                    if ($roleID > 0) {
                        $newRole =  new UserRole;
                        $newRole->FK_CORE_USER = $item->ID;
                        $newRole->FK_CORE_ROLE = $roleID;
                        $newRole->save();
                    }
                }
            }
        }

        // 3. Save photo
        $file = $request->file('PHOTO');

        if ($file) {
            // Save in storage
            $fullpathsavedfile = Storage::disk('ftp')->put('/user/photos/' . $item->ID, $file);
            $item->PHOTO = $fullpathsavedfile;
            $item->save();
        }

        //4. Adres maken indien nodig
        if ($request->get('ADDRESS_FK_CORE_COUNTRY') != null) {
            if (!$item->address) {
                $address = new Address();
                $address->ACTIVE = true;
                $address->FK_CORE_COUNTRY = $request->get('ADDRESS_FK_CORE_COUNTRY');
                $address->save();
                $address->refresh();

                //Koppelen en opslaan
                $item->FK_CORE_ADDRESS = $address->ID;
                $item->save();
                $item->refresh();
            }

            //4.2 Velden bijwerken van adress
            $item->address->FK_CORE_COUNTRY = $request->get('ADDRESS_FK_CORE_COUNTRY');
            $item->address->ADDRESSLINE = $request->get('ADDRESS_ADDRESSLINE');
            $item->address->ZIPCODE = $request->get('ADDRESS_ZIPCODE');
            $item->address->CITY = $request->get('ADDRESS_CITY');
            $item->address->HOUSENUMBER = $request->get('ADDRESS_HOUSENUMBER');
            $item->address->save();
        }
    }

    public function anonimyze(Request $request)
    {
        $user = User::find($request->input('id'));
        if($user)
        {
            $user->FIRSTNAME                         = 'Anoniem';
            $user->PREPOSITION                       = NULL;
            $user->LASTNAME                          = 'Anoniem';
            $user->EMAILADDRESS                      = 'Anoniem@anoniem.nl';
            $user->PHONE                             = 'Anoniem';
            $user->PHONE_MOBILE                      = 'Anoniem';
            $user->PHONE_EMERGENCY                   = 'Anoniem';
            $user->DATE_OF_BIRTH                     = NULL;
            $user->REMARKS                           = 'Anoniem';

            $user->save();

            return response()->json([
                'success'=> true,
                'message' => KJLocalization::translate('Algemeen', 'Opgeslagen', 'Opgeslagen')
            ], 200);
        }
    }

    public function delete(int $id)
    {
        $item = $this->find($id);

        if ($item) {
            if ($item->ACTIVE) {
                $status = 'gearchiveerd';
            } else {
                $status = 'geactiveerd';
            }

            $item->ACTIVE = !$item->ACTIVE;
            $result = $item->save();

            return response()->json([
                'success' => $result,
                'message' => KJLocalization::translate('Algemeen', 'Item kon niet worden ' . $status, 'Item kon niet worden ' . $status)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => KJLocalization::translate('Algemeen', 'Item niet (meer) gevonden', 'Item niet (meer) gevonden')
            ]);
        }
    }

    public function currentUser(Request $request)
    {
        return response()->json([
            'user' => Auth::guard('admin')->user(),
            'success'=> true
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $id = $request->get('id');
        $email = $request->get('email');

        $user = User::find($id);

        // If email invalid error
        if ($email != $user->EMAILADDRESS) {
            return response()->json([
                'success' => false,
                'message' => KJLocalization::translate('Admin - Werknemers', 'E-mailadres bericht ongeldig', 'Het opgegeven e-mailadres komt niet overeen met het e-mailadres van de werknemer. Wachtwoord kan niet worden gereset'). '!'
            ], 200);
        }

        try {
            $pw = User::generatePassword();
            $user->PASSWORD = $pw;
            $user->save();

            $user->sendWelcomeEmail();

            return response()->json([
                'success'=> true
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 200);
        }
    }

}