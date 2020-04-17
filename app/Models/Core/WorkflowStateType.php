<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use KJLocalization;

class WorkflowStateType extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'CORE_WORKFLOWSTATETYPE';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function getTitleAttribute()
    {
        $name = $this->DESCRIPTION;
        if (!$this->ACTIVE) {
            $name .= ' (' . strtolower(KJLocalization::translate('Algemeen', 'Inactief', 'Inactief')) . ')';
        }

        return $name;
    }

    public function project_type()
    {
        return $this->hasOne(DropdownValue::class, 'ID', 'FK_CORE_DROPDOWNVALUE');
    }

    public function products()
    {
        return $this->hasMany(WorkflowProduct::class, 'FK_CORE_WORKFLOWSTATETYPE', 'ID');
    }

    public function createProduct($product)
    {
        foreach ($product as $product_id) {
            $item = new WorkflowProduct([
                'ACTIVE' => true,
                'FK_CORE_WORKFLOWSTATETYPE' => $this->ID,
                'FK_ASSORTMENT_PRODUCT' => $product_id
            ]);
            $item->save();
        }

        return $item;
    }

    public function createCompetence($competence)
    {
        foreach ($competence as $competence_id) {
            $nextSequence = collect(DB::select('EXEC [SEQUENCE_NEXT] @TABLE = ?, @WHERE_FIELDS = ?, @WHERE_VALUES = ?', [
                'CORE_WORKFLOWSTATETYPE_ASSORTMENT_COMPETENCE',
                'FK_CORE_WORKFLOWSTATETYPE',
                $this->ID,
            ]))->first();

            $item = new WorkflowCompetence([
                'ACTIVE' => true,
                'SEQUENCE' => $nextSequence->SEQUENCE,
                'FK_CORE_WORKFLOWSTATETYPE' => $this->ID,
                'FK_ASSORTMENT_COMPETENCE' => $competence_id
            ]);
            $item->save();
        }

        return $item;
    }
}