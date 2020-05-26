<?php namespace App\Console\Commands\User;

use App\Models\Admin\Core\Notification;
use App\Models\Admin\Task\Task;
use App\Models\Admin\User;
use Illuminate\Console\Command;
use KJLocalization;

class ReminderTaskNotification extends Command {

    protected $name = 'exsist:reminder_task_notifaction';

    protected $description = 'Automatically notify when tasks have reached their reminder date';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $today = date('Y-m-d');
        $reminderTasks = Task::where([
            'ACTIVE' => true,
            'DONE' => false,
            'REMINDER_DATE' => $today,

        ])->get();
//        dd($reminderTasks);
        foreach ($reminderTasks as $reminderTask)
        {
            $notification = new Notification([
                'RECIPIENT_FK_CORE_USER' => $reminderTask->assignee->ID,
                'DATE' => date('Y-m-d'),
                'SUBJECT' => KJLocalization::translate('Admin - Taken', 'Taak herinnering', 'Taak herinnering: :TASK_DESCRIPTION - :TASK_DETAILS', [
                    'USER' => $reminderTask->assignee->title,
                    'TASK_DESCRIPTION' => $reminderTask->SUBJECT,
                    'TASK_DETAILS' => $reminderTask->CONTENT
                ]),
                'CONTENT' => KJLocalization::translate('Admin - Taken', 'Taak notificatie deadline date', 'Deadline: :DATE', [
                    'DATE' => $reminderTask->getDeadlineDatePickerFormattedAttribute(),
                ]),
                'SOURCE_TABLE' => $reminderTask->getTable(),
                'SOURCE_ID' => $reminderTask->ID,
                'SOURCE_URL' => '/admin/tasks/detail/' . $reminderTask->ID,
                'READED' => false
            ]);
            $notification->save();
        }
    }

}