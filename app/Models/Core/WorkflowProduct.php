<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class WorkflowProduct extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_WORKFLOWSTATETYPE_ASSORTMENT_PRODUCT';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function product()
    {
        return $this->hasOne(\App\Models\Admin\Assortment\Product::class, 'ID', 'FK_ASSORTMENT_PRODUCT');
    }

}