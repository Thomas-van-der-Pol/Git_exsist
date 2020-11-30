<?php

namespace App\Http\Controllers\Admin\Settings\Finance;

use App\Models\Admin\Core\Label;
use App\Models\Admin\CRM\Contact;
use App\Models\Admin\Finance\Ledger;
use App\Models\Admin\Finance\VAT;
use App\Models\Core\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use KJ\Core\controllers\AdminBaseController;
use Illuminate\Http\Request;
use KJLocalization;

class FinanceController extends AdminBaseController
{
    protected $model = 'App\Models\Admin\Core\Label';

    protected $mainViewName = 'admin/settings/finance/main';

    protected $allColumns = [
        'ID',
        'ACTIVE',
        'DESCRIPTION'
    ];

    protected $datatableFilter = array(
        ['ACTIVE', array(
            'param' => 'ACTIVE',
            'default' => true
        )]
    );

    protected $detailScreenFolder = 'admin.settings.finance.detail_screens';
    protected $detailViewName = 'admin/settings/finance/detail';

    protected $saveUnsetValues = [
//        'LOGO_EMAIL',
        'requester_table',
        'requester_item',
        'PROXY_NAME'
    ];

//    protected $saveValidation = [
//        'LOGO_EMAIL' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
//    ];

    protected function authorizeRequest($method, $parameters)
    {
        return ( Auth::guard()->user()->hasPermission(config('permission.FACTURATIE')) && Auth::guard()->user()->hasPermission(config('permission.INSTELLINGEN')));
    }

//    public function __construct()
//    {
//        parent::__construct();
//
//        $this->saveValidationMessages = [
//            'LOGO_EMAIL.uploaded' => KJLocalization::translate('Algemeen', 'Afbeelding te groot', 'Kan afbeelding niet uploaden. De maximale bestandsgrootte is 2 MB.'),
//            'LOGO_EMAIL.max' => KJLocalization::translate('Algemeen', 'Afbeelding te groot', 'Kan afbeelding niet uploaden. De maximale bestandsgrootte is 2 MB.')
//        ];
//    }

    public function index()
    {
        $labels = Label::where('ACTIVE', true);
        if ($labels->count() == 1) {
            return redirect('/admin/settings/finance/detail/' . $labels->first()->ID);
        }

        return parent::index();
    }

    protected function beforeDetailScreen(int $id, $item, $screen)
    {
        $bindings = [];

        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        switch ($screen) {
            case 'settings':
                $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];
                $contactsRelationProxyOri = Contact::where('FK_CRM_RELATION', $item->FK_CRM_RELATION_PROXY)->pluck('FULLNAME_ATTN', 'ID');
                $contactsRelationProxy = $none + $contactsRelationProxyOri->toArray();

                $ledgersOri = Ledger::select(DB::raw("CONCAT(ACCOUNT,' - ',DESCRIPTION) AS COMBINEDDESCRIPTION"),'ID','ACTIVE', 'FK_CORE_LABEL')
                    ->where('ACTIVE', TRUE)
                    ->where('FK_CORE_LABEL', $item->ID)
                    ->orderBy('COMBINEDDESCRIPTION')
                    ->pluck('COMBINEDDESCRIPTION', 'ID');
                $ledgers = $none + $ledgersOri->toArray();

                $vatOri = VAT::where('ACTIVE', true)
                    ->where('FK_CORE_LABEL', $item->ID)
                    ->orderBy('DESCRIPTION')
                    ->pluck('DESCRIPTION', 'ID');
                $vat = $none + $vatOri->toArray();

                $bindings = array_merge($bindings, [
                    ['ledgers', $ledgers],
                    ['vat', $vat],
                    ['contactsRelationProxy', $contactsRelationProxy]
                ]);
                break;
        }

        return $bindings;
    }

//    protected function afterSave($item, $originalItem, Request $request, &$response)
//    {
//        // 1. Save e-mail logo
//        $file = $request->file('LOGO_EMAIL');
//
//        if ($file) {
//            // Save in storage
//            $fullpathsavedfile = Storage::disk('ftp')->put('/label/logo/' . $item->ID, $file);
//            $item->LOGO_EMAIL = $fullpathsavedfile;
//            $item->save();
//        }
//    }

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

    public function uploadFile(Request $request)
    {
        $result = false;

        $id = $request->get('id');
        $item = $this->find($id);

        $file = $request->file('file');
        if ($file) {
            // Save in storage
            $fullpathsavedfile = $file->storeAs('/'.$item->getTable().'/'.$item->ID, $file->getClientOriginalName(), ['disk' => 'ftp_docservice']);
            $accessableFile = str_replace('public/','storage/', $fullpathsavedfile);

            // Get file info
            $documentInfo = pathinfo($accessableFile);

            // Link to model
            $document = new Document([
                'FK_TABLE' => $item->getTable(),
                'FK_ITEM' => $item->ID,
                'UPLOADER_FK_TABLE' => Auth::guard()->user()->getTable(),
                'UPLOADER_FK_ITEM' => Auth::guard()->user()->ID,
                'FILEPATH' => str_replace('/', '\\', $accessableFile),
                'FILESIZE' => $file->getSize(),
                'TITLE' => $documentInfo['filename'],
                'FILETYPE' => ($file->getClientOriginalExtension() ?? 'file')
            ]);

            $result = $document->save();

            if ($result) {
                $item->FK_DOCUMENT_PDF_PAPER = $document->ID;
                $item->save();
            }
        }

        return response()->json([
            'success' => $result,
            'document' => ($document ?? null)
        ]);
    }

    public function deleteFile(int $id)
    {
        $document = Document::find($id);
        $item = $this->find($document->FK_ITEM);

        if ($document) {
            $item->FK_DOCUMENT_PDF_PAPER = null;
            $item->save();

            $document->delete();

            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false
            ]);
        }
    }
}