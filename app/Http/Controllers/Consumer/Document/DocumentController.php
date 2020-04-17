<?php

namespace App\Http\Controllers\Consumer\Document;

use App\Models\Admin\Project\Document\Collection;
use KJ\Core\controllers\BaseController;

class DocumentController extends BaseController {

    protected $guard = 'auth:document';

    protected $mainViewName = 'consumer.document.main';

    public function indexCustom($GUID)
    {
        if ($this->mainViewName == '') {
            abort(400, 'Geen main view opgegeven! Vul variabele mainViewName.');
        }

        $collection = Collection::where('GUID', $GUID)->first();

        $view = view($this->mainViewName)
            ->with('collection', $collection);

        return $view;
    }

}
