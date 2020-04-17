<?php

namespace App\Http\Controllers\Admin\CRM\Contact;

use App\Libraries\Core\DropdownvalueUtils;
use App\Mail\Admin\CRM\NewPassword;
use App\Models\Admin\Core\Language;
use App\Models\Admin\CRM\Contact;
use App\Models\Admin\CRM\ContactLanguage;
use App\Models\Admin\CRM\ContactNationality;
use App\Models\Admin\CRM\Relation;
use App\Models\Core\DropdownValue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use KJ\Core\controllers\AdminBaseController;
use Illuminate\Http\Request;
use KJLocalization;

class ContactController extends AdminBaseController {

    protected $model = 'App\Models\Admin\CRM\Contact';

    protected $allColumns = ['ID', 'ACTIVE', 'FULLNAME', 'EMAILADDRESS', 'PHONENUMBER', 'FK_CORE_DROPDOWNVALUE_SALUTATION', 'INITIALS'];

    protected $datatableDefaultSort = array(
        [
            'field' => 'FULLNAME',
            'sort'  => 'ASC'
        ]
    );

    protected $datatableFilter = [
        ['FULLNAME, EMAILADDRESS, PHONENUMBER', array(
            'param' => 'ADM_CONTACT_FILTER_SEARCH',
            'operation' => 'like',
            'default' => ''
        )],
        ['ACTIVE', array(
            'param' => 'ACTIVE',
            'default' => true
        )]
    ];

    protected $detailViewName = 'admin.crm.relation.contact.detail';

    protected $saveUnsetValues = [
    ];

    protected $saveValidation = [
        'PARENTID' => 'required',
        'LASTNAME' => 'required',
        'EMAILADDRESS' => 'nullable|email'
    ];

//    protected function authorizeRequest($method, $parameters)
//    {
//        return ( Auth::guard()->user()->hasPermission(config('permission.CRM_FAMILIES')) || Auth::guard()->user()->hasPermission(config('permission.CRM_CLIENTS')) );
//    }

    public function indexRendered()
    {
        $view = view('admin.crm.relation.contact.modal');

        $extraBindings = $this->beforeIndex();
        if ($extraBindings != []) {
            foreach ($extraBindings as $binding) {
                $view->with($binding[0], $binding[1]);
            }
        }

        return response()->json([
            'viewDetail' => $view->render()
        ]);
    }

    public function allByRelationDatatable(Request $request, int $ID)
    {
        $this->whereClause = array(
            ['FK_CRM_RELATION', $ID],
        );

        return parent::allDatatable($request);
    }

    protected function beforeDetail(int $ID, $item)
    {
        $genders      = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_GENDER'));
        $salutions    = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_SALUTATION'));
        $attentionsto = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_ATTN'));
        $status = DropdownvalueUtils::getStatusDropdown();

        $bindings = array(
            ['genders', $genders],
            ['salutions', $salutions],
            ['attentionsto', $attentionsto],
            ['status', $status],
        );

        return $bindings;
    }

    public function detailDefault(Request $request, int $ID)
    {
        return parent::detailAsJSON($request, $ID);
    }

    public function save(Request $request)
    {
        $this->saveParentIDField = 'FK_CRM_RELATION';
        $this->saveExtraValues = [];

        // Validate email address
        $id = $request->get('ID');
        $email = ($request->get('EMAILADDRESS') ? $request->get('EMAILADDRESS') : '');

        if ($email != '') {
            if (!Contact::isEmailValid($id, $email)) {
                return response()->json([
                    'message' => KJLocalization::translate('Admin - CRM', 'Email address already in use', 'Email address already in use'),
                    'success'=> false
                ], 200);
            }
        }

        return parent::save($request);
    }

    public function anonimyze(Request $request)
    {
        $id = ( $request->get('id') ?? 0 );
        $contact = Contact::find($id);

        if($contact) {
            $contact->FK_CORE_DROPDOWNVALUE_GENDER      = NULL;
            $contact->INITIALS                          = NULL;
            $contact->FIRSTNAME                         = NULL;
            $contact->PREPOSITION                       = NULL;
            $contact->LASTNAME                          = 'Anonymized';
            $contact->PHONENUMBER                       = NULL;
            $contact->CELLPHONENUMBER                   = NULL;
            $contact->EMAILADDRESS                      = 'anonymized@anonymized.com';
            $contact->save();

            return response()->json([
                'success'=> true,
                'message' => KJLocalization::translate('Algemeen', 'Opgeslagen', 'Opgeslagen')
            ], 200);
        }
    }

    public function delete(int $ID)
    {
        $relationContact = $this->find($ID);

        if ($relationContact) {
            $relationContact->ACTIVE = false;
            $relationContact->save();

            return response()->json([
                'success' => true,
                'message' => KJLocalization::translate('Admin - CRM', 'Contactpersoon inactief gezet', 'Contactpersoon is succesvol inactief gezet'),
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => KJLocalization::translate('Admin - CRM', 'Geen contactpersoon gevonden', 'Er is geen contactpersoon gevonden'),
            ], 200);
        }
    }

    public function allByRelation(int $ID)
    {
        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        $relation = Relation::find($ID);

        $contactsOri = Contact::all()
            ->where('ACTIVE', true)
            ->where('FK_CRM_RELATION', $ID)
            ->pluck('FULLNAME', 'ID');

        $contacts = $none + $contactsOri->toArray();

        return response()->json([
            'financial_contact' => (int)($relation->FK_CRM_CONTACT_FINANCE ?? null),
            'items' => $contacts
        ], 200);
    }
}