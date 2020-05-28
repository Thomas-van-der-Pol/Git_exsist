<?php

namespace App\Http\Controllers\Core;

use App\Libraries\Core\DropdownvalueUtils;
use App\Models\Core\DropdownValue;
use App\Models\Core\Translation;
use KJ\Core\controllers\AdminBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use KJLocalization;

class DropdownValueController extends AdminBaseController
{

    protected $model = 'App\Models\Core\DropdownValue';

    protected $allColumns = ['ID', 'ACTIVE', 'SEQUENCE', 'TL_VALUE'];

    protected $joinClause = [
        [
            'TABLE' => 'CORE_TRANSLATION',
            'PRIMARY_FIELD' => 'FK_CORE_TRANSLATION_KEY',
            'FOREIGN_FIELD' => 'CORE_DROPDOWNVALUE.TL_VALUE',
            'TYPE' => 'LEFT'
        ]
    ];

    protected $datatableDefaultSort = array(
        [
            'field' => 'SEQUENCE',
            'sort'  => 'ASC'
        ],
        [
            'field' => 'CORE_TRANSLATION.TEXT',
            'sort'  => 'ASC'
        ]
    );

    protected $datatableFilter = array(
        ['ACTIVE', array(
            'param' => 'ACTIVE',
            'default' => true
        )],
    );

    protected $detailViewName = 'core.dropdownvalue.detail';

    protected $saveParentIDField = 'FK_CORE_DROPDOWNTYPE';

    protected $saveValidation = [];

    protected $saveValidationMessages = [];

    protected $saveUnsetValues = [
        'TRANS_TL_VALUE'
    ];

    protected function beforeIndex()
    {
        $status = DropdownvalueUtils::getStatusDropdown();

        $bindings = array(
            ['status', $status]
        );

        return $bindings;
    }

    public function indexRendered() {
        $this->mainViewName = 'core.dropdownvalue.main';

        $view = $this->index();

        return response()->json([
            'viewDetail' => $view->render()
        ]);
    }

    public function indexSelectableRendered() {
        $this->mainViewName = 'core.dropdownvalue.select';

        $view = $this->index();

        return response()->json([
            'viewDetail' => $view->render()
        ]);
    }

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('VALUE', function(DropdownValue $value) {
            return $value->VALUE;
        })->addColumn('SEQUENCE', function(DropdownValue $value) {
            if($value->SEQUENCE){
                return $value->getSequenceFormattedAttribute();
            } else {
                return '';
            }
        });
    }

    public function allByTypeDatatable(Request $request, int $ID)
    {
        $localeId = config('app.locale_id') ? config('app.locale_id') : config('language.defaultLangID');

        $this->whereClause = array(
            ['FK_CORE_DROPDOWNTYPE', $ID],
            ['CORE_TRANSLATION.FK_CORE_LANGUAGE', $localeId]
        );

        return parent::allDatatable($request);
    }

    protected function beforeDetail(int $ID, $item)
    {
        $status = DropdownvalueUtils::getStatusDropdown();

        $bindings = array(
            ['status', $status]
        );

        return $bindings;
    }

    protected function afterSave($item, $originalItem, Request $request, &$response)
    {
        //0. Let op belangrijk om te refreshen omdat er translations aangemaaakt zijn
        $item->refresh();

        //1. Vertaalvelden opslaan
        Translation::saveKJTranslationInput($item->TL_VALUE, $request->input('TRANS_TL_VALUE'));
    }

    public function save(Request $request)
    {
        $this->saveValidation = [
            'TRANS_TL_VALUE.'.config('language.defaultLangID') => 'required'
        ];

        $this->saveValidationMessages = [
            'TRANS_TL_VALUE.'.config('language.defaultLangID').'.required' => KJLocalization::translate('Algemeen', 'Translationfield name is required', 'Translationfield name is required')
        ];

        return parent::save($request);
    }

    public function allByTypeRendered(Request $request, int $ID)
    {
        $items = DropdownvalueUtils::getDropdown($ID);

        return response()->json([
            'results' => $items
        ], 200);

    }


}