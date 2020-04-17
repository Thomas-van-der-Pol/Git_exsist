<?php

namespace App\Http\Controllers\Core;

use App\Models\Core\ContentItem;
use App\Models\Core\Translation;
use KJ\Core\controllers\AdminBaseController;
use Illuminate\Http\Request;
use KJLocalization;

class ContentItemController extends AdminBaseController
{
    protected $model = 'App\Models\Core\ContentItem';

    protected $allColumns = ['ID', 'ACTIVE', 'SEQUENCE', 'TL_TITLE', 'TL_CONTENT'];

    protected $datatableDefaultSort = array(
        [
            'field' => 'SEQUENCE',
            'sort'  => 'ASC'
        ]
    );

    protected $datatableFilter = array(
        ['ACTIVE', array(
            'param' => 'ACTIVE',
            'default' => true
        )],
    );

    protected $detailViewName = 'core.contentitem.detail';

    protected $saveUnsetValues = [
        'TRANS_TL_TITLE',
        'TRANS_TL_CONTENT'
    ];

    protected function newItem(Request $request)
    {
        $table = $request->get('FK_TABLE');
        $itemId = $request->get('FK_ITEM');

        $maxSequence = ContentItem::where('FK_TABLE', $table)->where('FK_ITEM', $itemId)->max('SEQUENCE');

        $newItem = new ContentItem([
            'FK_TABLE' => $table,
            'FK_ITEM' => $itemId,
            'ACTIVE' => true,
            'SEQUENCE' => (($maxSequence ?? 0) + 1)
        ]);
        $newItem->save();
        $newItem->refresh();

        // Add default title
        $titleArray[(config('app.locale_id') ? config('app.locale_id') : config('language.defaultLangID'))] = KJLocalization::translate('Admin - Content', 'New chapter', 'New chapter');
        Translation::saveKJTranslationInput($newItem->TL_TITLE, $titleArray);

        $items = ContentItem::where('FK_TABLE', $table)->where('FK_ITEM', $itemId)->get();

        $view = view('core.contentitem.main')
            ->with('contentItems', $items);

        return response()->json([
            'id' => $newItem->ID,
            'viewDetail' => $view->render()
        ]);
    }

    protected function afterSave($item, $originalItem, Request $request, &$response)
    {
        // 0. Let op belangrijk om te refreshen omdat er translations aangemaaakt zijn
        $item->refresh();

        // 1. Vertaalvelden opslaan
        Translation::saveKJTranslationInput($item->TL_TITLE, $request->input('TRANS_TL_TITLE'));
        Translation::saveKJTranslationInput($item->TL_CONTENT, $request->input('TRANS_TL_CONTENT'));

        // 2. Nieuwe veldwaarden toevoegen voor direct refresh
        $response['item_title'] = $item->getTitleAttribute();
        $response['item_content'] = $item->getContentAttribute();
    }

    public function save(Request $request)
    {
        $this->saveValidation = [
            'TRANS_TL_TITLE.'.config('language.defaultLangID') => 'required'
        ];

        $this->saveValidationMessages = [
            'TRANS_TL_TITLE.'.config('language.defaultLangID').'.required' => KJLocalization::translate('Admin - Content', 'Title is required', 'Title is required')
        ];

        return parent::save($request);
    }

    public function delete(int $id)
    {
        $contentItem = ContentItem::find($id);

        if ($contentItem) {
            $contentItem->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function updateSequence(Request $request)
    {
        $chapters = $request->get('content-item-card');
        $currentSequence = 1;

        foreach ($chapters as $chapter)
        {
            ContentItem::find((int)$chapter)->update([
                'SEQUENCE' => $currentSequence
            ]);

            $currentSequence++;
        }
    }
}