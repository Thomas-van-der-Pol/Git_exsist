<?php

namespace App\Models\Admin\Project;


use App\Models\Admin\CRM\Contact;
use App\Models\Admin\CRM\Relation;
use App\Models\Admin\Finance\Invoice;
use App\Models\Admin\User;
use App\Models\Core\DropdownValue;
use App\Models\Core\WorkflowState;
use Illuminate\Database\Eloquent\Model;
use KJ\Core\libraries\KJUtils;
use KJ\Localization\libraries\LanguageUtils;
use KJLocalization;

class Project extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'PROJECT';
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
//        if($this->employer){
//            $name = $this->employer->title . ', ' . $this->employer_contact->title;
//        }
//        else{
//            $name = $this->employee->title;
//        }
        $name = $this->DESCRIPTION;
        if (!$this->ACTIVE) {
            $name .= ' (' . strtolower(KJLocalization::translate('Algemeen', 'Inactief', 'Inactief')) . ')';
        }

        return $name;
    }

    public function referrer()
    {
        return $this->hasOne(Relation::class, 'ID', 'FK_CRM_RELATION_REFERRER');
    }

    public function referrer_contact()
    {
        return $this->hasOne(Contact::class, 'ID', 'FK_CRM_CONTACT_REFERRER');
    }

    public function employer()
    {
        return $this->hasOne(Relation::class, 'ID', 'FK_CRM_RELATION_EMPLOYER');
    }

    public function employer_contact()
    {
        return $this->hasOne(Contact::class, 'ID', 'FK_CRM_CONTACT_EMPLOYER');
    }

    public function employee()
    {
        return $this->hasOne(Contact::class, 'ID', 'FK_CRM_CONTACT_EMPLOYEE');
    }

    public function type()
    {
        return $this->hasOne(DropdownValue::class, 'ID', 'FK_CORE_DROPDOWNVALUE_PROJECTTYPE');
    }

    public function workflowstate()
    {
        return $this->hasOne(WorkflowState::class, 'ID', 'FK_CORE_WORKFLOWSTATE');
    }

    public function user_created()
    {
        return $this->hasOne(User::class, 'ID', 'CREATE_FK_CORE_USER');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'FK_PROJECT', 'ID');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'FK_PROJECT', 'ID');
    }

    public function hasInvoices()
    {
        return ($this->invoices->where('ACTIVE', true)->count() > 0);
    }

    public function progress()
    {
        if (!$this->workflowstate) {
            return 0;
        }

        $maxSequence = WorkflowState::where([
            'ACTIVE' => true,
            'FK_CORE_WORKFLOWSTATETYPE' => config('workflowstate_type.TYPE_PROJECT')
        ])->max('SEQUENCE') - 1;

        $currentSequence = $this->workflowstate->SEQUENCE - 1;

        if ($currentSequence <= 0 || $maxSequence <= 0) {
            return 0;
        } else {
            return floor(($currentSequence / $maxSequence) * 100);
        }
    }

    public function getCreatedDateFormattedAttribute()
    {
        if ($this->TS_CREATED) {
            return date(LanguageUtils::getDateTimeFormat(), strtotime($this->TS_CREATED));
        } else {
            return '';
        }
    }

    public function getStartDateFormattedAttribute()
    {
        if ($this->START_DATE) {
            return date(LanguageUtils::getDateFormat(), strtotime($this->START_DATE));
        } else {
            return '';
        }
    }

    public function getCompensationPercentageDecimalAttribute()
    {
        if ($this->COMPENSATION_PERCENTAGE) {
            return number_format($this->COMPENSATION_PERCENTAGE,2, '.', '.');
        } else {
            return '';
        }
    }

    public function getCompensationPercentageFormattedAttribute()
    {
        if ($this->COMPENSATION_PERCENTAGE) {
            return number_format($this->COMPENSATION_PERCENTAGE,2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator()) . '%';
        } else {
            return '';
        }
    }

    public function getLastModifiedStateFormattedAttribute()
    {
        if($this->TS_LASTMODIFIED_STATE) {
            return KJUtils::time_since(time() - strtotime($this->TS_LASTMODIFIED_STATE));
        } else {
            return '';
        }
    }

    public function createProduct($product, $startDate,$assignee)
    {
        $productProject = null;

        foreach ($product as $product_id) {

            $product = \App\Models\Admin\Assortment\Product::find($product_id);

            $productProject = new \App\Models\Admin\Project\Product([
                'ACTIVE' => true,
                'FK_PROJECT' => $this->ID,
                'FK_ASSORTMENT_PRODUCT' => $product_id,
                'PRICE' => $product->PRICE,
                'QUANTITY' => 1,
                'DESCRIPTION_EXT' => $product->DESCRIPTION_EXT
            ]);
            $productProject->save();

            if($productProject) {
                //find intervention
                if($product) {
                    //find standard invoice schemes of intervention
                    //copy standard invoice schemes and change some values.
                    foreach ($product->invoiceSchemes->where('FK_PROJECT_ASSORTMENT_PRODUCT', null)->where('ACTIVE', true) as $invoiceScheme) {
                        $newInvoiceScheme = $invoiceScheme->replicate();
                        $newInvoiceScheme->FK_PROJECT_ASSORTMENT_PRODUCT = $productProject->ID;
                        $newInvoiceScheme->DATE = date('Y-m-d', strtotime($startDate . ' + ' . $invoiceScheme->DAYS . ' days'));
                        $newInvoiceScheme->save();
                    }

                    //find standard tasks of intervention
                    //copy standard tasks and change some values.
                    foreach ($product->tasks->where('FK_PROJECT_ASSORTMENT_PRODUCT', null)->where('ACTIVE', true) as $task) {
                        $task = $task->replicate();
                        $task->FK_PROJECT_ASSORTMENT_PRODUCT = $productProject->ID;
                        $task->FK_CORE_USER_ASSIGNEE = $assignee->ID;
                        $task->FK_PROJECT = $this->ID;
                        $task->REMINDER_DATE = date('Y-m-d', strtotime($startDate . ' + ' . $task->REMEMBER_DATES . ' days'));
                        $task->DEADLINE = date('Y-m-d', strtotime($startDate . ' + ' . $task->EXPIRATION_DATES . ' days'));
                        $task->REMEMBER_DATES = null;
                        $task->EXPIRATION_DATES = null;
                        $task->save();
                    }
                }
            }
        }

        return $productProject;
    }

}