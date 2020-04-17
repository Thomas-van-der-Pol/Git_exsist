<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class DocumentLog extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'DOCUMENT_LOG';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

}