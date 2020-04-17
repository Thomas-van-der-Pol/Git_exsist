<?php

namespace App\Http\Controllers\Admin\Settings\Language;

use App\Models\Core\Translation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use KJ\Core\controllers\AdminBaseController;
use App\Models\Core\Role;
use App\Models\Core\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use KJLocalization;

class LanguageController extends AdminBaseController
{
    protected $exceptGuard = ['switchLang'];
    protected $exceptAuthorization = ['switchLang'];

    protected $model        = 'App\Models\Admin\Core\Language';

    protected $mainViewName = 'admin/settings/language/main';

    protected $allColumns = [
        'ID',
        'ACTIVE',
        'SEQUENCE',
        'TL_DESCRIPTION',
        'CORE_TRANSLATION.TEXT AS DESCRIPTION'
    ];

    protected $joinClause = [
        [
            'TABLE'             => 'CORE_TRANSLATION',
            'PRIMARY_FIELD'     => 'FK_CORE_TRANSLATION_KEY',
            'FOREIGN_FIELD'     => 'TL_DESCRIPTION',
            'TYPE'              => 'LEFT'
        ]
    ];

    protected $datatableFilter = array(
        ['ACTIVE', array(
            'param' => 'ACTIVE',
            'default' => true
        )]
    );

    protected $detailViewName = 'admin/settings/language/detail';

    protected $saveUnsetValues = [
        'TRANS_TL_DESCRIPTION',
    ];

//    protected function authorizeRequest($method, $parameters)
//    {
//        return Auth::guard()->user()->hasPermission(config('permission.SETTINGS_LANGUAGE'));
//    }

    public function allDatatable(Request $request)
    {
        $localeId = config('language.defaultLangID');

        $this->whereClause = [
            ['CORE_TRANSLATION.FK_CORE_LANGUAGE', $localeId]
        ];

        return parent::allDatatable($request);
    }

    public function save(Request $request)
    {
        $this->saveValidation = [
            'TRANS_TL_DESCRIPTION.' . config('language.defaultLangID') => 'required'
        ];

        $this->saveValidationMessages = [
            'TRANS_TL_DESCRIPTION.' . config('language.defaultLangID') => KJLocalization::translate('Admin - Language', 'Description is required', 'Description is required')
        ];

        return parent::save($request);
    }

    protected function afterSave($item, $originalItem, Request $request, &$response)
    {
        //0. Let op belangrijk om te refreshen omdat er translations aangemaakt zijn
        $item->refresh();

        // Save translations
        Translation::saveKJTranslationInput($item->TL_DESCRIPTION, $request->input('TRANS_TL_DESCRIPTION'));
    }

    public function switchLang($locale)
    {
        $languages = array_column(config('language.langs'), 'CODE');
        if (in_array(strtoupper($locale), $languages)) {
            App::setLocale($locale);
            Session::put('applocale', strtolower($locale));
        }

        // Language vooraan toevoegen
        $parsedUrl = parse_url(URL::previous());
        $segments = [];
        if (isset($parsedUrl['path'])) {
            $segments = explode('/', parse_url(URL::previous())['path']);
        }
        $segments[1] = strtolower($locale);
        $newurl = implode('/', $segments);

        return redirect($newurl);
    }
}