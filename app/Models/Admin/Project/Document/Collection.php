<?php

namespace App\Models\Admin\Project\Document;

use App\Models\Admin\Project\Project;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'DOCUMENT_COLLECTION';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function project()
    {
        return $this->hasOne(Project::class, 'ID', 'FK_PROJECT');
    }

    public function contacts()
    {
        return $this->hasMany(CollectionContact::class, 'FK_DOCUMENT_COLLECTION', 'ID');
    }

    public function documents()
    {
        return $this->hasMany(CollectionDocument::class, 'FK_DOCUMENT_COLLECTION', 'ID');
    }

    public function hasContact($id)
    {
        $contact = $this->contacts->where('FK_CRM_CONTACT', $id)->first();

        return ($contact != null);
    }

    public function isExpiredForContact($id)
    {
        $contact = $this->contacts->where('FK_CRM_CONTACT', $id)->first();

        $now = new DateTime();
        $expirationDate = new DateTime($contact->EXPIRATION_DATE);

        return ($expirationDate < $now);
    }
}