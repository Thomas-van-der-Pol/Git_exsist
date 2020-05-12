<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Libraries\Core\DropdownvalueUtils;
use App\Models\Admin\Country\Country;
use App\Models\Admin\CRM\Address;
use App\Models\Admin\Finance\Invoice;
use Illuminate\Support\HtmlString;
use KJ\Core\controllers\AdminBaseController;
use Illuminate\Http\Request;
use KJLocalization;

class AddressController extends AdminBaseController {

    protected $model = 'App\Models\Admin\CRM\Address';

    protected $allColumns = ['ID', 'ACTIVE', 'FK_CRM_RELATION', 'FK_CORE_DROPDOWNVALUE_ADRESSTYPE', 'FK_CORE_ADDRESS'];

    protected $detailViewName = 'admin.crm.address.detail';

    protected $joinClause = [
        [
            'TABLE' => 'CORE_ADDRESS',
            'PRIMARY_FIELD' => 'ID',
            'FOREIGN_FIELD' => 'CRM_RELATION_ADDRESS.FK_CORE_ADDRESS',
        ]
    ];

    protected $saveValidation = [
        'PARENTID' => 'required',
        'ADDRESSLINE' => 'required'
    ];

    protected $saveUnsetValues = [
        'FK_CORE_COUNTRY',
        'ZIPCODE',
        'HOUSENUMBER',
        'ADDRESSLINE',
        'CITY'
    ];

    protected $datatableFilter = [
        ['ADDRESSLINE, ZIPCODE, CITY, HOUSENUMBER', array(
            'param' => 'ADM_FILTER_ADRES_STATUS',
            'operation' => 'like',
            'default' => ''
        )]
    ];

    protected $saveParentIDField = 'FK_CRM_RELATION';

    public function allByRelationDatatable(Request $request, int $ID)
    {
        $this->whereClause = array(
            ['FK_CRM_RELATION', $ID]
        );

        return parent::allDatatable($request);
    }

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('ADDRESS_TYPE', function(Address $address) {
            return isset($address->address->addressType) ? $address->address->addressType->getValueAttribute() : '';
        })->addColumn('FULL_ADDRESS', function(Address $address) {
            return new HtmlString(nl2br($address->address->fullAddress(FALSE)));
        });
    }

    protected function beforeDetail(int $ID, $item)
    {
        $none           = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];
        $countriesOri   = Country::all()->where('ACTIVE', true)->sortBy('country_name')->pluck('country_name', 'ID');
        $countries      = $none + $countriesOri->toArray();
        $addresstypes   = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_ADDRESSTYPE'),FALSE);

        $bindings = array(
            ['countries', $countries],
            ['addresstypes', $addresstypes]
        );

        return $bindings;
    }

    protected function afterSave($item, $originalItem, Request $request, &$response)
    {
        //Opslaan/bijwerken van gekopeld address
        $address = $item->address;
        if( ! $address ) {
            $address = new \App\Models\Admin\Core\Address();
        }

        $address->FK_CORE_COUNTRY = $request->get('FK_CORE_COUNTRY');
        $address->FK_CORE_DROPDOWNVALUE_ADRESSTYPE = $request->get('FK_CORE_DROPDOWNVALUE_ADRESSTYPE');
        $address->ADDRESSLINE = $request->get('ADDRESSLINE');
        $address->ZIPCODE = $request->get('ZIPCODE');
        $address->CITY = $request->get('CITY');
        $address->HOUSENUMBER = $request->get('HOUSENUMBER');
        $address->save();

        $address->refresh();

        //Koppelen
        $item->FK_CORE_ADDRESS = $address->ID;
        $item->save();
    }

    public function replicate(Request $request)
    {
        $id             = ( $request->get('id') ?? 0 );
        $address        = Address::find($id);

        //Eerst adres duplicaten
        $subAddress = $address->address->duplicate();
        //Adres dupliceren
        $newAddress     = $address->duplicate();
        //Koppelen
        $newAddress->FK_CORE_ADDRESS = $subAddress->ID;
        $newAddress->save();


        return response()->json([
            'success' => ($newAddress != null),
            'address' => $newAddress
        ]);
    }

    public function delete(int $id)
    {
        $item = $this->find($id);

        $address = $item->address;

        $invoice = Invoice::where([
            'FK_CRM_RELATION_ADDRESS' => $id
        ])->count();

        if($invoice > 0) {
            return response()->json([
                'success' => false,
                'message' => KJLocalization::translate('Admni - CRM', 'Er staat een factuur gekoppeld aan dit adres', 'Er staat een factuur gekoppeld aan dit adres')
            ]);
        }

        if ($item) {
            $item->delete();
            $address->delete();

            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => KJLocalization::translate('Algemeen', 'Item niet (meer) gevonden', 'Item niet (meer) gevonden')
            ]);
        }
    }

    public function allByRelation(int $ID)
    {
        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        $addressesOri = Address::all()
            ->where('ACTIVE', true)
            ->where('FK_CRM_RELATION', $ID)
            ->where('FK_CORE_DROPDOWNVALUE_ADRESSTYPE', config('dropdown_type.TYPE_ADDRESSTYPE_VALUE.INVOICE'))
            ->pluck('fullAddress', 'ID');

        $addresses = $none + $addressesOri->toArray();

        return response()->json([
            'items' => $addresses
        ], 200);
    }

}