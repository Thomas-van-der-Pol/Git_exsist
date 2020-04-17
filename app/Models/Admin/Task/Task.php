<?php

namespace App\Models\Admin\Task;

use App\Models\Admin\Assortment\Product;
use App\Models\Admin\CRM\Relation;
use App\Models\Admin\CustomMap;
use App\Models\Admin\Project\Project;
use App\Models\Admin\User;
use App\Models\Core\DropdownValue;
use Illuminate\Database\Eloquent\Model;
use KJ\Core\libraries\KJUtils;
use KJ\Localization\libraries\LanguageUtils;
use KJLocalization;

class Task extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'TASK';
    protected $primaryKey = 'ID';

    protected $guarded = ['ID'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function assignee()
    {
        return $this->hasOne(User::class, 'ID', 'FK_CORE_USER_ASSIGNEE');
    }

    public function user_created()
    {
        return $this->hasOne(User::class, 'ID', 'FK_CORE_USER_CREATED');
    }

    public function user_done()
    {
        return $this->hasOne(User::class, 'ID', 'FK_CORE_USER_DONE');
    }
    public function user_started()
    {
        return $this->hasOne(User::class, 'ID', 'FK_CORE_USER_STARTED');
    }

    public function progress(){
        if($this->DONE){
            return 2;
        }
        elseif($this->STARTED){
            return 1;
        }
        else{
            return 0;
        }
    }

    public function subscriptions()
    {
        return $this->hasMany(TaskSubsription::class, 'FK_TASK', 'ID');
    }

    public function relation()
    {
        return $this->hasOne(Relation::class, 'ID', 'FK_CRM_RELATION');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'ID', 'FK_PROJECT');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'ID', 'FK_ASSORTMENT_PRODUCT');
    }

    public function taskList()
    {
        return $this->hasOne(TaskList::class, 'ID', 'FK_TASK_LIST');
    }

    public function categories()
    {
        return $this->hasMany(Filter::class, 'FK_TASK', 'ID');
    }

    public function customMaps()
    {
        return $this->belongsToMany(CustomMap::class, 'TASK_USER_CUSTOM_MAP', 'FK_TASK', 'FK_USER_CUSTOM_MAP');

    }

    public function categoriesAsText()
    {
        $categoriesTexts = "";
        foreach($this->categories()->pluck('FK_CORE_DROPDOWNVALUE') as $categoryValue){
            $categoriesTexts .= ','.DropdownValue::find($categoryValue)->getValueAttribute();
        }
        return $categoriesTexts;
    }


    public function getDeadlineDatePickerFormattedAttribute()
    {
        if($this->DEADLINE) {
            return date(LanguageUtils::getDateFormat(), strtotime($this->DEADLINE));
        } else {
            return '';
        }
    }
    public function getReminderDateDatePickerFormattedAttribute()
    {
        if($this->DEADLINE) {
            return date(LanguageUtils::getDateFormat(), strtotime($this->REMINDER_DATE));
        } else {
            return '';
        }
    }

    public function getDeadlineFormattedAttribute()
    {
//        if($this->DEADLINE) {
//            $deadline = new Carbon($this->DEADLINE);
//            $now = Carbon::now();
//            $difference = ($deadline->diff($now)->days < 1)
//                ? KJLocalization::translate('Datumtijd', 'vandaag in kleine letters', 'vandaag')
//                : $deadline->diffForHumans($now);
//
//            return $difference;
//        } else {
//            return KJLocalization::translate('Admin - Taken', 'geen deadline', 'geen deadline');
//        }
        if($this->TS_LASTMODIFIED_STATE) {
            return KJUtils::time_since(time() - strtotime($this->TS_LASTMODIFIED_STATE));
        } else {
            return '';
        }

    }

    public function getDoneFormattedAttribute()
    {
        return ($this->DONE == true) ? KJLocalization::translate('Admin - Taken', 'Gereed', 'Gereed') : KJLocalization::translate('Admin - Taken', 'Openstaand', 'Openstaand');
    }

    public function getCreatedDateFormattedAttribute()
    {
        if ($this->TS_CREATED) {
            return date(LanguageUtils::getDateTimeFormat(), strtotime($this->TS_CREATED));
        } else {
            return '';
        }
    }

    public function getDoneDateFormattedAttribute()
    {
        if ($this->DONE_DATE) {
            return date(LanguageUtils::getDateTimeFormat(), strtotime($this->DONE_DATE));
        } else {
            return '';
        }
    }
    public function getStartedDateFormattedAttribute()
    {
        if ($this->STARTED_DATE) {
            return date(LanguageUtils::getDateTimeFormat(), strtotime($this->STARTED_DATE));
        } else {
            return '';
        }
    }

    public function getContentFormattedAttribute( $content, $length = 30)
    {
        if( strlen($content) <= $length )
            return $content;

        $parts = explode(' ', $content);

        while( strlen( implode(' ', $parts) ) > $length )
            array_pop($parts);

        return implode(' ', $parts) . '...';
    }
}