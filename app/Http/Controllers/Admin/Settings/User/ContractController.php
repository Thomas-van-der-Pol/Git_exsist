<?php

namespace App\Http\Controllers\Admin\Settings\User;

use App\Libraries\Core\DropdownvalueUtils;
use App\Models\Admin\Core\Notification;
use App\Models\Admin\Core\Role;
use App\Models\Admin\Core\UserContract;
use App\Models\Admin\Core\UserContractDay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use KJ\Core\controllers\AdminBaseController;
use Illuminate\Http\Request;
use KJLocalization;

class ContractController extends AdminBaseController
{
    protected $model = 'App\Models\Admin\Core\UserContract';

    protected $allColumns = ['ID','ACTIVE', 'FK_CORE_DROPDOWNVALUE_USERCONTRACTTYPE', 'DATE_START', 'DATE_END', 'DATE_PROBATION'];

    protected $datatableFilter = array(
        ['ACTIVE', array(
            'param' => 'ACTIVE',
            'default' => true
        )]
    );

    protected $datatableDefaultSort = array(
        [
            'field' => 'DATE_START',
            'sort'  => 'DESC'
        ]
    );

    protected $detailViewName = 'admin.settings.user.contract.detail';

    protected $saveValidation = [
        'FK_CORE_DROPDOWNVALUE_USERCONTRACTTYPE'    => 'required',
        'HOURS'                                     => 'required',
        'HOURS_WEEKLY'                              => 'required',
        'DATE_START'                                => 'required',
    ];

    protected $saveUnsetValues = [
        'HOURS_EVEN',
        'HOURS_ODD',
        'DUMMY_NOTIFICATION',
        'DUMMY_FK_CORE_ROLE_NOTIFICATION',
        'DUMMY_DATE_NOTIFICATION'
    ];

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('DATE_START_FORMATTED', function(UserContract $usercontract) {
                return $usercontract->getDateStartFormattedAttribute() ?? '';
            })
            ->addColumn('END_START_FORMATTED', function(UserContract $usercontract) {
                return $usercontract->getDateEndFormattedAttribute() ?? '';
            })
            ->addColumn('CONTRACTTYPE', function(UserContract $usercontract) {
                return isset($usercontract->contracttype) ? $usercontract->contracttype->Value : '';
            });
    }

    public function allByUserDatatable(Request $request, int $ID)
    {
        $this->whereClause = array(
            ['FK_CORE_USER', $ID],
        );

        return parent::allDatatable($request);
    }

    protected function beforeDetail(int $ID, $item)
    {
        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        $contracttypes = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_USERCONTRACTTYPE'),false);
        $days = UserContractDay::where([
            'ACTIVE' => true,
            'FK_CORE_USER_CONTRACT' => $ID
        ])->get();

        $rolesOri = Role::where([
            'ACTIVE' => true
        ])->orderBy('DESCRIPTION')->pluck('DESCRIPTION', 'ID');
        $roles = $none + $rolesOri->toArray();

        $bindings = [
            ['contracttypes', $contracttypes],
            ['days', $days],
            ['roles', $roles]
        ];

        return $bindings;
    }

    public function save(Request $request)
    {
        $this->saveParentIDField = 'FK_CORE_USER';
        $this->saveExtraValues = [];

        $probation  = $request->get('DATE_PROBATION');
        $start = $request->get('DATE_START');
        $end = $request->get('DATE_END');

        // Check contracts between inserted period
        if($start > '') {
            $start_date = date('Y-m-d', strtotime($start));
            $end_date = date('Y-m-d', strtotime($end));

            $duplicate = UserContract::where('FK_CORE_USER', $request->get('PARENTID'))
                ->where(function ($q) use ($start_date, $end_date) {
                    $q->whereRaw("(DATE_START >= ? AND DATE_END <= NULLIF(?, '1970-01-01'))", [$start_date, $end_date]) // @Van en @Tot tussen bestaande regel
                    ->orWhereRaw("(? >= DATE_START AND NULLIF(?, '1970-01-01') <= DATE_END)", [$start_date, $end_date]) // Van en Tot tussen @Van en @Tot
                    ->orWhereRaw("(NULLIF(?, '1970-01-01') >= DATE_START AND NULLIF(?, '1970-01-01') <= DATE_END)", [$end_date, $end_date]) // @Tot tussen Van en Tot
                    ->orWhereRaw("(? >= DATE_START AND ? <= DATE_END)", [$start_date, $start_date]) // @Van tussen Van en Tot
                    ->orWhereRaw("DATE_START <= ? AND DATE_END IS NULL", [$start_date]); // Contact zonder einddatum in het verleden
                });

            if ($request->get('ID') != $this->newRecordID) {
                $duplicate->where('ID', '<>', $request->get('ID'));
            }

            if($duplicate->count() > 0) {
                return response()->json([
                    'message' => KJLocalization::translate('Admin - Werknemers', 'Er bestaat al een contract met deze datum', 'Er bestaat al een contract met deze datum'),
                    'success'=> false
                ], 200);
            }
        }

        // Validate contract dates
        if ($probation > '') {
            //Probation bigger then start
            if(strtotime($start) > strtotime($probation)) {
                return response()->json([
                    'message' => KJLocalization::translate('Admin - Werknemers', 'Einde proeftijd dient groter te zijn dan startdatum', 'Einde proeftijd dient groter te zijn dan startdatum'),
                    'success'=> false
                ], 200);
            }
        }

        //Check end date
        if ($end > '') {
            if(strtotime($start) > strtotime($end)) {
                return response()->json([
                    'message' => KJLocalization::translate('Admin - Werknemers', 'Einddatum dient groter te zijn dan startdatum', 'Einddatum dient groter te zijn dan startdatum'),
                    'success'=> false
                ], 200);
            }
        }

        return parent::save($request);
    }

    protected function afterSave($item, $originalItem, Request $request, &$response)
    {
        $hoursEvens = ( $request->input('HOURS_EVEN') ?? 0 );
        $hoursOdds = ( $request->input('HOURS_ODD') ?? 0 );

        // Save even weeks
        foreach($hoursEvens as $key => $value) {
            $contractDay = UserContractDay::firstOrCreate([
                'FK_CORE_USER_CONTRACT' => $item->ID,
                'DAY' => $key,
                'EVEN_WEEK' => true
            ]);
            $contractDay->ACTIVE = true;
            $contractDay->HOURS = $value;
            $contractDay->save();
        }

        // Save odd weeks
        foreach($hoursOdds as $key => $value) {
            $contractDay = UserContractDay::firstOrCreate([
                'FK_CORE_USER_CONTRACT' => $item->ID,
                'DAY' => $key,
                'EVEN_WEEK' => false
            ]);
            $contractDay->ACTIVE = true;
            $contractDay->HOURS = $value;
            $contractDay->save();
        }

        // Save notification
        $notification_insert = $request->get('DUMMY_NOTIFICATION');
        $notification_role = $request->get('DUMMY_FK_CORE_ROLE_NOTIFICATION');
        $notification_date = date('Y-m-d', strtotime($request->get('DUMMY_DATE_NOTIFICATION')));

        if ($notification_insert == true) {
            $role = Role::find($notification_role);
            if ($role) {
                $users = $role->userRoles;
                foreach ($users as $user) {
                    $notification = new Notification([
                        'RECIPIENT_FK_CORE_USER' => $user->FK_CORE_USER,
                        'DATE' => $notification_date,
                        'SUBJECT' => KJLocalization::translate('Admin - Werknemers', 'Contract notificatie titel', 'Contract van :USER verloopt binnenkort', [
                            'USER' => $item->user->title
                        ]),
                        'CONTENT' => KJLocalization::translate('Admin - Werknemers', 'Contract notificatie tekst', 'Het contract van :USER met startdatum :START verloopt op :END', [
                            'USER' => $item->user->title,
                            'START' => $item->DateStartFormatted,
                            'END' => $item->DateEndFormatted
                        ]),
                        'SOURCE_TABLE' => $item->getTable(),
                        'SOURCE_ID' => $item->ID,
                        'SOURCE_URL' => '/admin/settings/user/detail/' . $item->FK_CORE_USER,
                        'READED' => false
                    ]);
                    $notification->save();
                }
            }
        }
    }

}