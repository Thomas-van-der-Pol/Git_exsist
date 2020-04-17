<?php

namespace App\Libraries\Consumer;

use App\Models\Admin\Project\Document\Collection;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\Auth;
use KJ\Core\models\FileRequest;
use KJLocalization;

class FileRequestUtils
{

    public static function download(FileRequest $fileRequest)
    {
        // Validate collection
        $collectionGuid = session('document_collection_guid', null);
        if ($collectionGuid == null) {
            return abort(403);
        }

        $collection = Collection::where('GUID', $collectionGuid)->first();
        if (!$collection) {
            return abort(404);
        }

        if (!$collection->hasContact(Auth::guard('document')->user()->ID)) {
            return abort(403);
        }

        // Set expiration date on collection at first download
        $contact = $collection->contacts->where('FK_CRM_CONTACT', Auth::guard('document')->user()->ID)->first();
        if ($contact->DOWNLOADED == false) {
            $expirationDate = new DateTime();
            $expirationDate->add(new DateInterval('P1D')); // Now +1 day

            $contact->EXPIRATION_DATE = $expirationDate;
            $contact->DOWNLOADED = true;
            $contact->save();
        }

        // Download file
        return \KJ\Core\libraries\FileRequestUtils::download($fileRequest);
    }

    public static function upload(FileRequest $fileRequest, $file)
    {
        return \KJ\Core\libraries\FileRequestUtils::upload($fileRequest, $file);
    }

}