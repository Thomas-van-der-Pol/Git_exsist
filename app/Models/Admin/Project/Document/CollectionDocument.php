<?php

namespace App\Models\Admin\Project\Document;

use App\Models\Core\Document;
use Illuminate\Database\Eloquent\Model;

class CollectionDocument extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'DOCUMENT_COLLECTION_DOCUMENT';
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

    public function document()
    {
        return $this->hasOne(Document::class, 'ID', 'FK_DOCUMENT');
    }
}