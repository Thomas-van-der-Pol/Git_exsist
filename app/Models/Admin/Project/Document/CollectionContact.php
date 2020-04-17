<?php

namespace App\Models\Admin\Project\Document;

use App\Mail\Admin\Project\ShareDocuments;
use App\Models\Admin\CRM\Contact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use KJ\Localization\libraries\LanguageUtils;

class CollectionContact extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'DOCUMENT_COLLECTION_CONTACT';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function collection()
    {
        return $this->hasOne(Collection::class, 'ID', 'FK_DOCUMENT_COLLECTION');
    }

    public function contact()
    {
        return $this->hasOne(Contact::class, 'ID', 'FK_CRM_CONTACT');
    }

    public function notifyContact()
    {
        if (!$this->contact->routeNotificationForMail() > '') {
            return;
        }

        Mail::to($this->contact->routeNotificationForMail())->send(new ShareDocuments($this->collection));
    }

    public function getExpirationDateFormattedAttribute()
    {
        if ($this->EXPIRATION_DATE) {
            return date(LanguageUtils::getDateTimeFormat(), strtotime($this->EXPIRATION_DATE));
        } else {
            return '';
        }
    }
}