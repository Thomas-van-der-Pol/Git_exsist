<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use KJ\Core\models\FileRequest;
use KJ\Localization\libraries\LanguageUtils;
use KJLocalization;

class Document extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'DOCUMENT';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function(Document $document) {
            if ($document->FILETYPE != 'dir') {
                Storage::disk('ftp')->delete($document->FILEPATH);
            }

            foreach ($document->logs as $log)
            {
                $log->delete();
            }
        });
    }

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function filetype()
    {
        return $this->hasOne(DocumentFileType::class, 'FILETYPE', 'FILETYPE');
    }

    public function logs()
    {
        return $this->hasMany(DocumentLog::class, 'FK_DOCUMENT','ID');
    }

    public function uploader()
    {
        $item = DB::table($this->UPLOADER_FK_TABLE)->where('ID', $this->UPLOADER_FK_ITEM)->first();
        return ($item->FULLNAME ?? KJLocalization::translate('Documenten', 'Onbekend', 'Onbekend'));
    }

    public function lastDownload()
    {
        $item = FileRequest::where([
            'OBJECT' => $this->getTable(),
            'OBJECT_ID' => $this->ID,
            'DOWNLOADED' => true
        ])->first();

        if ($item)
        {
            $requester = DB::table($item->REQUEST_OBJECT)->where('ID', $item->REQUEST_OBJECT_ID)->first();

            return [
                'TIME' => $item->getCreationDateFormattedAttribute(),
                'REQUESTER' => ($requester->FULLNAME ?? KJLocalization::translate('Documenten', 'Onbekend', 'Onbekend'))
            ];
        }

        return [];
    }

    public function getDocumentInformationAttribute()
    {
        $info = "<b>".KJLocalization::translate('Documenten', 'Geupload', 'Geupload')."</b>: <br/>";
        $info .= date(LanguageUtils::getDateTimeFormat(), strtotime($this->TS_CREATED)) . "<br/>";
        $info .= KJLocalization::translate('Documenten', 'door', 'door') . " " . $this->uploader();

        $lastDownload = $this->lastDownload();
        if ($lastDownload != []) {
            $info .= "<br/><br/><b>".KJLocalization::translate('Documenten', 'Laatst gedownload', 'Laatst gedownload')."</b>: <br/>";
            $info .= $lastDownload['TIME'] . "<br/>";
            $info .= KJLocalization::translate('Documenten', 'door', 'door') . " " . $lastDownload['REQUESTER'];
        }

        return $info;
    }

    public function getFileTypeFormattedAttribute()
    {
        if ($this->filetype) {
            return $this->filetype->DESCRIPTION;
        } else {
            return $this->FILETYPE;
        }
    }

    function getLastModifiedFormattedAttribute()
    {
        if ($this->TS_LASTMODIFIED) {
            return date(LanguageUtils::getDateTimeFormat(), strtotime($this->TS_LASTMODIFIED));
        } else {
            return '';
        }
    }

    function getFileSizeFormattedAttribute($unit = "") {
        return self::getFileSize($this->FILESIZE, $unit);
    }

    public static function getFileSize($fileSize, $unit = "") {
        if( (!$unit && $fileSize >= 1<<30) || $unit == "GB")
            return number_format($fileSize/(1<<30),2)." GB";
        if( (!$unit && $fileSize >= 1<<20) || $unit == " MB")
            return number_format($fileSize/(1<<20),2)." MB";
        if( (!$unit && $fileSize >= 1<<10) || $unit == " KB")
            return number_format($fileSize/(1<<10),2)." KB";
        return number_format($fileSize)." bytes";
    }

}