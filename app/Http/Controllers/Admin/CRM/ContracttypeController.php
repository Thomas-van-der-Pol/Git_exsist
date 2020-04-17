<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Models\Admin\CRM\Contracttype;
use App\Models\Core\Translation;
use Illuminate\Support\Facades\Auth;
use KJ\Core\controllers\AdminBaseController;
use Illuminate\Http\Request;
use KJLocalization;

class ContracttypeController extends AdminBaseController {

    protected $model = 'App\Models\Admin\CRM\Contracttype';

    protected $allColumns = ['ID', 'ACTIVE', 'FK_CRM_CLIENT', 'TL_TITLE', 'TL_VALUE', 'TL_TITLE.TEXT AS TRANS_TITLE', 'TL_VALUE.TEXT AS TRANS_VALUE'];

    protected $joinClause = [
        [
            'TABLE'             => 'CORE_TRANSLATION',
            'ALIAS'             => 'TL_TITLE',
            'FOREIGN_FIELD'     => 'TL_TITLE.FK_CORE_TRANSLATION_KEY',
            'PRIMARY_FIELD'     => 'CRM_CLIENT_CONTRACTTYPE.TL_TITLE',
            'TYPE'              => 'left'
        ],
        [
            'TABLE'             => 'CORE_TRANSLATION',
            'ALIAS'             => 'TL_VALUE',
            'FOREIGN_FIELD'     => 'TL_VALUE.FK_CORE_TRANSLATION_KEY',
            'PRIMARY_FIELD'     => 'CRM_CLIENT_CONTRACTTYPE.TL_VALUE',
            'TYPE'              => 'left'
        ]
    ];

    protected $datatableDefaultSort = array(
        [
            'field' => 'TL_TITLE',
            'sort'  => 'ASC'
        ]
    );

    protected $detailViewName = 'admin.crm.client.contracttype.detail';

    protected $saveUnsetValues = [
        'TRANS_TL_TITLE',
        'TRANS_TL_VALUE'
    ];

//    protected function authorizeRequest($method, $parameters)
//    {
//        return ( Auth::guard()->user()->hasPermission(config('permission.CRM_FAMILIES')) || Auth::guard()->user()->hasPermission(config('permission.CRM_CLIENTS')) );
//    }

    public function allByRelationDatatable(Request $request, int $ID)
    {
        $this->whereClause = array(
            ['FK_CRM_CLIENT', $ID],
            ['ACTIVE', true]
        );

        return parent::allDatatable($request);
    }

    protected function afterSave($item, $originalItem, Request $request, &$response)
    {
        // Refresh item because of translations
        $item->refresh();

        // Save translations
        Translation::saveKJTranslationInput($item->TL_TITLE, $request->input('TRANS_TL_TITLE'));
        Translation::saveKJTranslationInput($item->TL_VALUE, $request->input('TRANS_TL_VALUE'));
    }

    public function save(Request $request)
    {
        $this->saveParentIDField = 'FK_CRM_CLIENT';
        $this->saveValidation = [
            'PARENTID' => 'required',
            'TRANS_TL_TITLE.' . config('language.defaultLangID') => 'required',
            'TRANS_TL_VALUE.' . config('language.defaultLangID') => 'required'
        ];

        $this->saveValidationMessages = [
            'TRANS_TL_TITLE.' . config('language.defaultLangID') => KJLocalization::translate('Admin - CRM', 'Title is required', 'Title is required'),
            'TRANS_TL_VALUE.' . config('language.defaultLangID') => KJLocalization::translate('Admin - CRM', 'Value is required', 'Value is required')
        ];

        return parent::save($request);
    }

}