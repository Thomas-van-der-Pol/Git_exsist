<?php

namespace App\Http\Controllers\Admin\Tasks;

use App\Libraries\Core\DropdownvalueUtils;
use App\Mail\Admin\Task\TaskNotification;
use App\Models\Admin\Core\Notification;
use App\Models\Admin\CRM\Contact;
use App\Models\Admin\CustomMap;
use App\Models\Admin\Project\Project;
use App\Models\Admin\Task\Filter;
use App\Models\Admin\Task\Task;
use App\Models\Admin\Task\TaskCustomMap;
use App\Models\Admin\Task\TaskList;
use App\Models\Admin\Task\TaskSubsription;
use App\Models\Admin\User;
use Cassandra\Custom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use KJ\Core\controllers\AdminBaseController;
use KJ\Core\libraries\SessionUtils;
use KJLocalization;

class TasksController extends AdminBaseController {

    protected $model = 'App\Models\Admin\Task\Task';

    protected $mainViewName = 'admin.tasks.main';

    protected $detailScreenFolder = 'admin.tasks.detail_screens';
    protected $detailViewName = 'admin.tasks.detail';

    protected $saveUnsetValues = [
        'USER_DONE',
        'DONE_DATE',
        'USER_CREATED',
        'USER_STARTED',
        'STARTED_DATE',
        'DATE_CREATED',
        'RELATION_NAME',
        'RELATION_PROJECT',
        'RELATION_PRODUCT',
        'RELATION_CONTACT_MOMENT',
        'PROGRESS',
        'CATEGORIES'
    ];

    protected function authorizeRequest($method, $parameters)
    {
        return Auth::guard()->user()->hasPermission(config('permission.TAKEN'));
    }

    public function modal(Request $request, int $id)
    {
        $this->mainViewName = 'admin.tasks.modal';

        $item = $this->find($id);

        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        $usersOri = User::all()->where('ACTIVE', true)->sortBy('title')->pluck('title', 'ID');
        $users = $none + $usersOri->toArray();

        // Determine lookup values
        $type = ( $request->get('type') ?? 0 );
        $pid = ( $request->get('pid') ?? 0 );

        $projects = null;
        switch ($type) {
            case config('task_type.TYPE_RELATION'):
//                @TODO DOMINIQUE: HAAL PROJECTEN (DOSSIERS) VAN DE HUIDIGE RELATIE OP
//                $projectsOri = Project::where([
//                    'ACTIVE' => true,
//                    'FK_CRM_RELATION_REFERRER' => $pid
//                ])->pluck('DESCRIPTION', 'ID');
//                $projects = $none + $projectsOri->toArray();
                $projects = $none;

                break;

            case config('task_type.TYPE_PROJECT'):
                break;
            case config('task_type.TYPE_TASKLIST'):
                break;
        }

        $view = $this->index()
            ->with('item', $item)
            ->with('users', $users)
            ->with('projects', $projects);

        return response()->json([
            'viewDetail' => $view->render()
        ]);
    }

    public function taskListModal(Request $request, int $id)
    {
        $type = ( $request->get('type') ?? 0 );
        if($type == config('task_type.TYPE_PROJECT')){
            $this->mainViewName = 'admin.tasks.listmodal';

            $item = $this->find($id);

            $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];
            $project = Project::find(( $request->get('pid') ?? 0 ));

            $taskListsOri = TaskList::all()->pluck('NAME', 'ID');
            $contactsOri = User::all()->where('ACTIVE',true)->pluck('FULLNAME', 'ID');
            $contacts = $none + $contactsOri->toArray();
            $taskLists = $none + $taskListsOri->toArray();

            $view = $this->index()
                ->with('item', $item)
                ->with('project', $project)
                ->with('taskLists', $taskLists)
                ->with('contacts', $contacts);

            return response()->json([
                'viewDetail' => $view->render()
            ]);
        }
        else{
            //@todo: melding veranderen
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

    }

    public function functionsModal(Request $request)
    {
        $type = ( $request->get('type') ?? 0 );

        $this->mainViewName = 'admin.tasks.functionmodal';

        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        $contactsOri = Contact::all()->pluck('FIRSTNAME', 'ID');
        $contacts = $none + $contactsOri->toArray();
        $customMapsOri = CustomMap::all()->where('FK_CORE_USER', Auth::guard()->user()->ID)->pluck('NAME', 'ID');
        $customMaps = $none + $customMapsOri->toArray();
        $view = $this->index()
            ->with('contacts', $contacts)
            ->with('customMaps', $customMaps)
            ->with('type', $type);

        return response()->json([
            'viewDetail' => $view->render()
        ]);
    }

    protected function beforeIndex()
    {
        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        $usersOri = User::all()->where('ACTIVE', true)->sortBy('title')->pluck('title', 'ID');
        $users = $none + $usersOri->toArray();
        $customMaps = CustomMap::all()->where('FK_CORE_USER', Auth::guard()->user()->ID);
        $filters = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_TASK_CATEGORY'));

        $bindings = array(
            ['users', $users],
            ['filters', $filters],
            ['customMaps', $customMaps]
        );

        return $bindings;
    }

    public function saveTaskList(Request $request){
        $userAssignee = $request->get('FK_CORE_USER_ASSIGNEE');
        $userCreated = $request->get('FK_CORE_USER_CREATED');
        $projectID = $request->get('FK_PROJECT');
        $startDate = $request->get('STARTDATE');
        $taskListID = $request->get('FK_TASK_LIST');
        $tasks = TaskList::find($taskListID)->tasks;
        foreach ($tasks as $task){
            $newTask = $task->replicate();
            $newTask->FK_TASK_LIST = null;
            $newTask->ACTIVE = true;
            $newTask->FK_CORE_USER_CREATED = $userCreated;
            $newTask->FK_CORE_USER_ASSIGNEE = $userAssignee;
            $newTask->FK_PROJECT = $projectID;
            $newTask->DEADLINE = date('Y-m-d', strtotime($startDate. ' + '.$task->EXPIRATION_DATES.' days'));
            $newTask->REMINDER_DATE = date('Y-m-d', strtotime($startDate. ' + '.$task->REMEMBER_DATES.' days'));
            $newTask->EXPIRATION_DATES = null;
            $newTask->REMEMBER_DATES = null;
            $newTask->save();
        }
        return response()->json([
            'success' => true
        ]);
    }

    public function beforeDetailScreen(int $id, $item, $screen)
    {

        $bindings = [];
        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        switch ($screen) {
            case 'default':
                $categories = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_TASK_CATEGORY'));
                $usersOri = User::all()->where('ACTIVE', true)->sortBy('title')->pluck('title', 'ID');
                $users = $none + $usersOri->toArray();
                $progressOptions = [
                    0 => KJLocalization::translate('Admin - Taken', 'Niet gestart', 'Niet gestart'),
                    1 => KJLocalization::translate('Admin - Taken', 'Gestart', 'Gestart'),
                    2 => KJLocalization::translate('Admin - Taken', 'Voltooid', 'Voltooid')
                ];
                $bindings = array_merge($bindings, [
                    ['users', $users],
                    ['categories', $categories],
                    ['progressOptions', $progressOptions]
                ]);
                break;
        }

        return $bindings;
    }

    protected function beforeRetrieveTasks($type, $pid, $page, $assignee,$category, $filter, $beginDate, $endDate) {
        $pageSize = 10;
        $bindings = [
            ['type', $type],
            ['pid', $pid]
        ];

        $forcedUser = 0;
        if (!Auth::guard()->user()->hasPermission(config('permission.TAKEN_INZIEN'))) {
            $forcedUser = Auth::guard()->user()->ID;
        }

        $items = [];
        $blockEdit = false;
        switch ($type) {
            case config('task_type.TYPE_OPEN'):
                $items = Task::where([
                    'FK_TASK_LIST' => null,
                    'ACTIVE' => true,
                    'DONE' => false
                ]);

                if (($assignee > 0) && ($forcedUser == 0)) {
                    $items->where([
                        'FK_CORE_USER_ASSIGNEE' => $assignee
                    ]);
                }
                break;

            case config('task_type.TYPE_DONE'):
                $items = Task::where([
                    'FK_TASK_LIST' => null,
                    'ACTIVE' => true,
                    'DONE' => true
                ]);

                if (($assignee > 0) && ($forcedUser == 0)) {
                    $items->where([
                        'FK_CORE_USER_ASSIGNEE' => $assignee
                    ]);
                }
                break;

            case config('task_type.TYPE_ALL'):
                $items = Task::where('ACTIVE', true)->whereNull('FK_TASK_LIST')->orderBy('DEADLINE', 'ASC');
                break;

            case config('task_type.TYPE_TODAY'):
                $date = date('Y-m-d');

                $items = Task::where([
                    'ACTIVE' => true,
                    'FK_TASK_LIST' => null,
                    'DEADLINE' => $date,
                    'DONE' => false
                ])
                ->orderBy('DEADLINE', 'ASC');

                if (($assignee > 0) && ($forcedUser == 0)) {
                    $items->where([
                        'FK_CORE_USER_ASSIGNEE' => $assignee
                    ]);
                }
                break;

            case config('task_type.TYPE_WEEK'):
                $begin_week = date('Y-m-d', strtotime('this week'));
                $end_week = date('Y-m-d', strtotime('first sunday'));

                $items = Task::whereBetween('DEADLINE', [$begin_week, $end_week])->where([
                    'ACTIVE' => true,
                    'FK_TASK_LIST' => null,
                    'DONE' => false
                ])
                ->orderBy('DEADLINE', 'ASC');

                if (($assignee > 0) && ($forcedUser == 0)) {
                    $items->where([
                        'FK_CORE_USER_ASSIGNEE' => $assignee
                    ]);
                }
                break;

            case config('task_type.TYPE_MONTH'):
                $begin_month = date('Y-m-d', strtotime('first day of this month'));
                $end_month = date('Y-m-d', strtotime('last day of this month'));

                $items = Task::whereBetween('DEADLINE', [$begin_month, $end_month])->where([
                    'ACTIVE' => true,
                    'FK_TASK_LIST' => null,
                    'DONE' => false
                ])
                ->orderBy('DEADLINE', 'ASC');

                if (($assignee > 0) && ($forcedUser == 0)) {
                    $items->where([
                        'FK_CORE_USER_ASSIGNEE' => $assignee
                    ]);
                }
                break;

            case config('task_type.TYPE_SUBSCRIBED'):
                $items = Task::where('ACTIVE', true)
                    ->whereNull('FK_TASK_LIST')
                    ->whereHas('subscriptions', function($filter) {
                        $filter->where('FK_CORE_USER', Auth::guard()->user()->ID);
                    })
                    ->orderBy('DEADLINE', 'ASC');
                break;

            case config('task_type.TYPE_RELATION'):
                $items = Task::where([
                    'ACTIVE' => true,
                    'DONE' => false,
                    'FK_CRM_RELATION' => $pid
                ])
                ->orderBy('DEADLINE', 'ASC');
                break;

            case config('task_type.TYPE_PROJECT'):
                $items = Task::where([
                    'ACTIVE' => true,
                    'DONE' => false,
                    'FK_PROJECT' => $pid
                ])
                ->orderBy('DEADLINE', 'ASC');

                $project = Project::find($pid);
                $blockEdit = (($project->INVOICING_COMPLETE ?? false) == true);
                break;

            case config('task_type.TYPE_PRODUCT'):
                $items = Task::where([
                    'ACTIVE' => true,
                    'DONE' => false,
                    'FK_ASSORTMENT_PRODUCT' => $pid
                ])
                    ->orderBy('DEADLINE', 'ASC');

                $project = Project::find($pid);
                $blockEdit = (($project->INVOICING_COMPLETE ?? false) == true);
                break;

            case config('task_type.TYPE_TASKLIST'):
                $items = Task::where([
                    'ACTIVE' => true,
                    'DONE' => false,
                    'FK_TASK_LIST' => $pid
                ])
                ->orderBy('EXPIRATION_DATES', 'ASC');
                break;
            default:

                $customMap = CustomMap::where('NAME', $type)->first();

                $items = Task::where([
                    'ACTIVE' => true,
                    'DONE' => false,
                ]);

                $items->whereHas('customMaps', function($q) use ($customMap)
                {
                    $q->where('NAME', $customMap->NAME);

                });
                $items->orderBy('EXPIRATION_DATES', 'ASC');
                break;
        }

        if (($assignee > 0) && ($forcedUser == 0)) {
            $items->where([
                'FK_CORE_USER_ASSIGNEE' => $assignee
            ]);
        }

        if($category > 0){
            $items = $items->whereHas('categories', function($q) use ($category)
            {
                $q->where('FK_CORE_DROPDOWNVALUE', $category);

            });
        }
        if($type != config('task_type.TYPE_TODAY') && $type != config('task_type.TYPE_WEEK') && $type != config('task_type.TYPE_MONTH')){
            if ($beginDate && $endDate) {
                $begin = date('Y-m-d H:i:s', strtotime($beginDate));
                $end = date('Y-m-d H:i:s', strtotime($endDate));

                $items->whereBetween('DEADLINE', [$begin, $end]);
            }
        }
        // Apply forced user
        if (($forcedUser > 0) && ($type != config('task_type.TYPE_SUBSCRIBED'))) {
            $items->where(function($query) use ($forcedUser) {
                $query->where('FK_CORE_USER_ASSIGNEE', $forcedUser)
                    ->orWhere('FK_CORE_USER_CREATED', $forcedUser);
            });
        }

        // Check if filter is empty, load default from session
        if ($filter == '') {
            $filter = SessionUtils::getSession('ADM_TASK', 'ADM_TASK_FILTER_SEARCH', '');
        }

        // Apply filter
        if ($filter != '') {
            $items->where(function($whereFilter) use ($filter) {
                $whereFilter->where('SUBJECT', 'like', '%' . $filter . '%');
                $whereFilter->orWhere('CONTENT', 'like', '%' . $filter . '%');
                $whereFilter->orWhereHas('project', function ($whereFilter) use ($filter) {
                    $whereFilter->where('DESCRIPTION', 'like', '%' . $filter . '%');
                });
            });
        }

        // Get item count
        $maxItems = $items->count();

        // Apply pagination
        $items->skip(($page - 1) * $pageSize)->take($pageSize);
        $items = $items->get();

        $bindings = array_merge($bindings, [
            ['items', $items],
            ['blockEdit', $blockEdit],
            ['maxItems', $maxItems],
            ['pageSize', $pageSize],
            ['page', $page],
            ['filter', $filter]
        ]);

        return $bindings;
    }

    public function retrieveTasks(Request $request)
    {
        $type = ( $request->get('TYPE') ?? 0 );
        $pid = ( $request->get('PID') ?? 0 );
        $screen = ( $request->get('SCREEN') ?? 0 );
        $assignee = ( $request->get('ASSIGNEE') ?? 0 );
        $category = ( $request->get('CATEGORY') ?? 0 );
        $filter = ( $request->get('FILTER') ?? '' );
        $page = ( $request->get('PAGE') ?? 0 );
        $beginDate = ( $request->get('BEGINDATE') ?? 0 );
        $endDate = ( $request->get('ENDDATE') ?? 0 );
//        dd($beginDate, $endDate);

        $tasksSubs = TaskSubsription::where('ACTIVE', true)
            ->where('FK_CORE_USER', Auth::guard()->user()->ID)
            ->get();

        $view = view('admin.tasks.items')
            ->with('type', $type)
            ->with('screen', $screen)
            ->with('assignee', $assignee)
            ->with('tasksSubs', $tasksSubs);

        $extraBindings = $this->beforeRetrieveTasks($type, $pid, $page, $assignee,$category, $filter, $beginDate, $endDate);
        if ($extraBindings != []) {
            foreach ($extraBindings as $binding) {
                $view->with($binding[0], $binding[1]);
            }
        }

        return response()->json([
            'success' => true,
            'view' => $view->render()
        ]);
    }

    protected function afterSave($item, $originalItem, Request $request, &$response)
    {
        $categoriesInput = json_decode($request->get('CATEGORIES'));
        $progress = $request->get('PROGRESS');
        // Check if done

        if($progress == 2){
            $item->DONE = true;
            $item->FK_CORE_USER_DONE = Auth::guard()->user()->ID;
            $item->DONE_DATE = date('Y-m-d H:i');
            $item->STARTED = false;

        }
        elseif ($progress == 1){

            $item->DONE = false;
            $item->FK_CORE_USER_DONE = null;
            $item->DONE_DATE = null;
            $item->STARTED = true;
            $item->FK_CORE_USER_STARTED = Auth::guard()->user()->ID;
            $item->STARTED_DATE = date('Y-m-d H:i');
        }
        else{

            $item->DONE = false;
            $item->FK_CORE_USER_DONE = null;
            $item->DONE_DATE = null;
            $item->STARTED = false;
            $item->FK_CORE_USER_STARTED = null;
            $item->STARTED_DATE = null;
        }
        $item->save();

        if($categoriesInput) {
            if (count($categoriesInput) > 0) {
                foreach ($categoriesInput as $categoryInput) {
                    $categories = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_TASK_CATEGORY'));
                    $taskCategoryID = array_search($categoryInput->value, $categories);
                    foreach ($item->categories as $category) {
                        $category->delete();
                    }
                    if ($taskCategoryID != null && $taskCategoryID > 0) {
                        $category = new Filter([
                            'FK_CORE_DROPDOWNVALUE' => $taskCategoryID
                        ]);
                        $item->categories()->save($category);
                    }
                }
            }
        }
        // Check if assignee has been changed
        if (($item->FK_CORE_USER_ASSIGNEE) && ($item->FK_CORE_USER_ASSIGNEE != ($originalItem->FK_CORE_USER_ASSIGNEE ?? null))) {
            // Insert notification
            $notification = new Notification([
                'ACTIVE' => true,
                'READED' => false,
                'SOURCE_TABLE' => $item->getTable(),
                'SOURCE_ID' => $item->ID,
                'SOURCE_URL' => '/admin/tasks/detail/' . $item->ID,
                'SUBJECT' =>  KJLocalization::translate('Admin - Taak', 'Taak aan je toegewezen', 'Taak aan je toegewezen'),
                'RECIPIENT_FK_CORE_USER' => $item->FK_CORE_USER_ASSIGNEE,
                'DATE' => date('Y-m-d')
            ]);
            $notification->save();

            Mail::to($item->assignee->EMAILADDRESS)->send(new TaskNotification($item));
        }
    }

    public function setSubscription(Request $request)
    {
        $id = ( $request->get('id') ?? 0 );
        $subscriptionId = ( $request->get('subscriptionId') ?? 0 );
        $result = false;

        if($subscriptionId > 0) {
            $taskSubsription = TaskSubsription::find($subscriptionId);

            if($taskSubsription) {
                $result = $taskSubsription->delete();
            }
        } else {
            $newTaskSub = TaskSubsription::firstOrCreate([
                'FK_CORE_USER' => Auth::guard()->user()->ID,
                'FK_TASK' => $id
            ]);

            $result = $newTaskSub->save();
        }

        return response()->json([
            'success' => $result
        ]);
    }

    public function setDone(Request $request) {
        $status = ( $request->get('status') ?? 0 );
        $tasks = json_decode(( $request->get('task') ?? 0 ), true);
        foreach ($tasks as $task_id) {
            $task = Task::where([
                'ID' => $task_id
            ])->first();

            if($status == 'setdone') {
                $task->DONE = true;
                $task->FK_CORE_USER_DONE = Auth::guard()->user()->ID;
                $task->DONE_DATE = date('Y-m-d H:i');
            } else {
                $task->DONE = false;
                $task->FK_CORE_USER_DONE = null;
                $task->DONE_DATE = null;
            }
            $result = $task->save();
        }

        return response()->json([
            'success' => ($result != null)
        ]);
    }

    public function shiftDeadline(Request $request)
    {
        $tasks = json_decode(($request->get('task') ?? 0), true);
        $days = $request->get('SHIFT_DATES');

        if ($tasks && $days) {
            foreach ($tasks as $taskID) {
                $task = Task::find($taskID);
                $task->update([
                    'DEADLINE' => date('Y-m-d', strtotime($task->DEADLINE . ' + ' . $days . ' days'))
                ]);
            }
        }
        return response()->json([
            'success' => true
        ]);
    }

    public function connectEmployee(Request $request){
        $tasks = json_decode(( $request->get('task') ?? 0 ), true);
        $userAssignee = $request->get('FK_CORE_USER_ASSIGNEE');
        if ($tasks && $userAssignee) {
            foreach ($tasks as $taskID) {
                $task = Task::find($taskID);
                $task->update([
                    'FK_CORE_USER_ASSIGNEE' => $userAssignee
                ]);
            }
        }
        return response()->json([
            'success' => true
        ]);
    }

    public function copyToMap(Request $request)
    {
        $tasks = json_decode(($request->get('task') ?? 0), true);
        $customMapID = $request->get('FK_USER_CUSTOM_MAP');
        $customMap = CustomMap::find($customMapID);

        if ($tasks && $customMapID) {
            foreach ($tasks as $taskID) {
                $task = Task::find($taskID);
                if(!$customMap->tasks->contains($task)){
                    $taskCustomMap = new TaskCustomMap([
                        'FK_TASK' => $taskID,
                        'FK_USER_CUSTOM_MAP' => $customMapID
                    ]);
                    $taskCustomMap->save();
                }
            }
        }
        return response()->json([
            'success' => true
        ]);
    }

    public function delete(int $id)
    {
        $item = $this->find($id);

        if ($item) {
            if ($item->ACTIVE) {
                $status = 'gearchiveerd';
            } else {
                $status = 'geactiveerd';
            }

            $item->ACTIVE = !$item->ACTIVE;
            $result = $item->save();

            return response()->json([
                'success' => $result,
                'message' => KJLocalization::translate('Algemeen', 'Item kon niet worden ' . $status, 'Item kon niet worden ' . $status)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => KJLocalization::translate('Algemeen', 'Item niet (meer) gevonden', 'Item niet (meer) gevonden')
            ]);
        }
    }
}