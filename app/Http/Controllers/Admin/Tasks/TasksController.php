<?php

namespace App\Http\Controllers\Admin\Tasks;

use App\Libraries\Core\DropdownvalueUtils;
use App\Mail\Admin\Task\TaskNotification;
use App\Mail\Consumer\Relocation\DocumentDeleted;
use App\Models\Admin\Assortment\Product;
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
use App\Models\Core\DropdownValue;
use App\Models\Core\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use KJ\Core\controllers\AdminBaseController;
use KJ\Core\libraries\SessionUtils;
use KJLocalization;

class TasksController extends AdminBaseController {

    protected $model = 'App\Models\Admin\Task\Task';

    protected $detailScreenFolder = 'admin.tasks.detail_screens';
    protected $detailViewName = 'admin.tasks.detail';

    protected $mainViewName = 'admin.tasks.main';

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

        $categories = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_TASK_CATEGORY'));

        // Determine lookup values
        $type = ( $request->get('type') ?? 0 );
        $pid = ( $request->get('pid') ?? 0 );

        $projects = null;
        $products = null;
        switch ($type) {
            case config('task_type.TYPE_RELATION'):
                $projectsOri = Project::where([
                    'ACTIVE' => true,
                    'FK_CRM_RELATION_EMPLOYER' => $pid
                ])->pluck('DESCRIPTION', 'ID');
                $projects = $none + $projectsOri->toArray();
                break;

            case config('task_type.TYPE_PROJECT'):
                if ($pid > 0) {
                    $products = \App\Models\Admin\Project\Product::with('product')->where([
                        'ACTIVE' => true,
                        'FK_PROJECT' => $pid
                    ])
                        ->get()
                        ->pluck('product.DESCRIPTION_INT', 'ID');

                    $products = $none + $products->toArray();
                }
                break;
            case config('task_type.TYPE_TASKLIST'):
                break;
        }

        $view = $this->index()
            ->with('item', $item)
            ->with('users', $users)
            ->with('categories', $categories)
            ->with('projects', $projects)
            ->with('products', $products);

        return response()->json([
            'viewDetail' => $view->render()
        ]);
    }

    public function taskListModal(Request $request, int $id)
    {
        $type = ( $request->get('type') ?? 0 );
        if($type == config('task_type.TYPE_PROJECT') || $type == config('task_type.TYPE_PRODUCT')){
            $this->mainViewName = 'admin.tasks.listmodal';

            $item = $this->find($id);

            $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];
            $project = Project::find(( $request->get('pid') ?? 0 ));
            $product = Product::find(( $request->get('pid') ?? 0 ));

            $taskListsOri = TaskList::where('ACTIVE', true)->orderBy('NAME')->pluck('NAME', 'ID');
            $contactsOri = User::all()->where('ACTIVE',true)->pluck('FULLNAME', 'ID');
            $contacts = $none + $contactsOri->toArray();
            $taskLists = $none + $taskListsOri->toArray();

            $products = null;
            if ($type == config('task_type.TYPE_PROJECT')) {
                $products = \App\Models\Admin\Project\Product::with('product')->where([
                    'ACTIVE' => true,
                    'FK_PROJECT' => $project->ID
                ])
                    ->get()
                    ->pluck('product.DESCRIPTION_INT', 'ID');
                $products = $none + $products->toArray();
            }

            $view = $this->index()
                ->with('item', $item)
                ->with('type', $type)
                ->with('project', $project)
                ->with('product', $product)
                ->with('taskLists', $taskLists)
                ->with('contacts', $contacts)
                ->with('products', $products);

            return response()->json([
                'viewDetail' => $view->render()
            ]);
        }
        else{
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
        $customMapsOri = CustomMap::all()->where('FK_CORE_USER', Auth::guard()->user()->ID)->sortByDesc('NAME')->pluck('NAME', 'ID');
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

        $customMaps = CustomMap::all()->where('FK_CORE_USER', Auth::guard()->user()->ID)->sortBy('NAME');
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
        $productId = $request->get('FK_ASSORTMENT_PRODUCT');
        $intervention = $request->get('FK_PROJECT_ASSORTMENT_PRODUCT');
        $type = $request->get('TYPE');
        $startDate = $request->get('STARTDATE');
        $taskListID = $request->get('FK_TASK_LIST');
        $tasks = TaskList::find($taskListID)->tasks->where('ACTIVE', true);
        foreach ($tasks as $task){
            $newTask = $task->replicate();
            $newTask->FK_TASK_LIST = null;
            $newTask->ACTIVE = true;
            $newTask->FK_CORE_USER_CREATED = $userCreated;

            switch ($type){
                case config('task_type.TYPE_PROJECT'):
                    $newTask->FK_PROJECT = $projectID;
                    $newTask->FK_ASSORTMENT_PRODUCT = null;
                    $newTask->FK_PROJECT_ASSORTMENT_PRODUCT = $intervention;
                    $newTask->DEADLINE = date('Y-m-d', strtotime($startDate. ' + '.$task->EXPIRATION_DATES.' days'));
                    $newTask->REMINDER_DATE = date('Y-m-d', strtotime($startDate. ' + '.$task->REMEMBER_DATES.' days'));
                    $newTask->EXPIRATION_DATES = null;
                    $newTask->REMEMBER_DATES = null;
                    $newTask->FK_CORE_USER_ASSIGNEE = $userAssignee;
                    break;
                case config('task_type.TYPE_PRODUCT'):
                    $newTask->FK_ASSORTMENT_PRODUCT = $productId;
                    $newTask->FK_PROJECT = null;
                    $newTask->DEADLINE = null;
                    $newTask->REMINDER_DATE = null;
                    $newTask->EXPIRATION_DATES = $task->EXPIRATION_DATES;
                    $newTask->REMEMBER_DATES = $task->REMEMBER_DATES;
                    $newTask->FK_CORE_USER_ASSIGNEE = null;
                    break;
            }

            $newTask->save();
            $newTask->refresh();
            if(count($task->categories) > 0){
                foreach($task->categories as $category){
                    $newCategoryLink = $category->replicate();
                    $newCategoryLink->FK_TASK = $newTask->ID;
                    $newCategoryLink->save();
                }
            }
        }
        return response()->json([
            'success' => true
        ]);
    }

    protected function beforeDetail(int $ID, $item)
    {

        $type = request('type');
        $bindings = [
          ['type', $type]
        ];
        return $bindings;
    }

    public function beforeDetailScreen(int $id, $item, $screen)
    {
        $bindings = [];
        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];
        $type = request('type');
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

                $products = [];
                if ($item && $item->FK_PROJECT) {
                    $products = \App\Models\Admin\Project\Product::with('product')->where([
                            'ACTIVE' => true,
                            'FK_PROJECT' => $item->FK_PROJECT
                        ])
                        ->get()
                        ->pluck('product.DESCRIPTION_INT', 'ID');

                    $products = $none + $products->toArray();
                }

                $bindings = array_merge($bindings, [
                    ['users', $users],
                    ['categories', $categories],
                    ['progressOptions', $progressOptions],
                    ['products', $products],
                    ['type', $type]
                ]);
                break;
        }

        return $bindings;
    }

    protected function beforeRetrieveTasks($type, $pid, $page, $assignee, $category, $filter, $beginDate, $endDate, $active, $statusTask) {
        $pageSize = 10;
        $status = DropdownvalueUtils::getStatusDropdown(false);
        $taskStatus = DropdownvalueUtils::getStatusDropdownTask();
        $categories = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_TASK_CATEGORY'));
        $bindings = [
            ['type', $type],
            ['pid', $pid],
            ['status', $status],
            ['taskStatus', $taskStatus],
            ['categories', $categories]
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
                    'ACTIVE' => $active,
                    'DONE' => false
                ])
                ->orderBy('DEADLINE', 'ASC');

                if (($assignee > 0) && ($forcedUser == 0)) {
                    $items->where([
                        'FK_CORE_USER_ASSIGNEE' => $assignee
                    ]);
                }
                break;

            case config('task_type.TYPE_DONE'):
                $items = Task::where([
                    'FK_TASK_LIST' => null,
                    'ACTIVE' => $active,
                    'DONE' => true
                ])
                ->orderBy('DEADLINE', 'ASC');

                if (($assignee > 0) && ($forcedUser == 0)) {
                    $items->where([
                        'FK_CORE_USER_ASSIGNEE' => $assignee
                    ]);
                }
                break;

            case config('task_type.TYPE_ALL'):
                $items = Task::where('ACTIVE', $active)->whereNull('FK_TASK_LIST')->orderBy('DEADLINE', 'ASC');
                break;

            case config('task_type.TYPE_TODAY'):
                $today = date('Y-m-d');
                $items = Task::where('DEADLINE', $today)->where([
                    'ACTIVE' => $active,
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

            case config('task_type.TYPE_WEEK'):
                $begin_week = date('Y-m-d', strtotime('this week'));
                $end_week = date('Y-m-d', strtotime('first sunday'));

                $items = Task::whereBetween('DEADLINE', [$begin_week,$end_week])->where([
                    'ACTIVE' => $active,
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

                $items = Task::whereBetween('DEADLINE', [$begin_month,$end_month])->where([
                    'ACTIVE' => $active,
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
                    'ACTIVE' => $active
                ]);
                if($statusTask != 0) {
                    if ($statusTask == 1) {
                        $items->where('STARTED', false)->where('DONE', false);
                    } else if ($statusTask == 2) {
                        $items->where('STARTED', true)->where('DONE', false);
                    } else if ($statusTask == 3) {
                        $items->where('STARTED', false)->where('DONE', true);
                    }
                }
                $items->where(function($filter) use ($pid) {
                    $filter->where('FK_CRM_RELATION', $pid)
                        ->orWhereHas('project', function ($projectFilter) use ($pid) {
                            $projectFilter->where('FK_CRM_RELATION_EMPLOYER', $pid);
                        });
                })
                ->orderBy('DEADLINE', 'ASC');
                break;

            case config('task_type.TYPE_PROJECT'):
                $items = Task::where([
                    'ACTIVE' => $active,
                    'FK_PROJECT' => $pid,
                ]);
                if($statusTask != 0) {
                    if ($statusTask == 1) {
                        $items->where('STARTED', false)->where('DONE', false);
                    } else if ($statusTask == 2) {
                        $items->where('STARTED', true)->where('DONE', false);
                    } else if ($statusTask == 3) {
                        $items->where('STARTED', false)->where('DONE', true);
                    }
                }
                $items->orderBy('DEADLINE', 'ASC');
                break;

            case config('task_type.TYPE_PRODUCT'):
                $items = Task::where([
                    'ACTIVE' => $active,
                    'DONE' => false,
                    'FK_ASSORTMENT_PRODUCT' => $pid,
                    'FK_TASK_LIST' => null,
                    'FK_CRM_RELATION' => null,
                    'FK_PROJECT' => null,
                ])
                ->orderBy('DEADLINE', 'ASC');
                break;

            case config('task_type.TYPE_TASKLIST'):
                $items = Task::where([
                    'ACTIVE' => $active,
                    'DONE' => false,
                    'FK_TASK_LIST' => $pid,
                    'FK_ASSORTMENT_PRODUCT' => null,
                    'FK_CRM_RELATION' => null,
                    'FK_PROJECT' => null,
                ])
                ->orderBy('EXPIRATION_DATES', 'ASC');
                break;

            default:
                $customMap = CustomMap::where('NAME', $type)->first();
                $items = Task::where([
                    'ACTIVE' => $active,
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

        // Apply date filter
        if($type != config('task_type.TYPE_TODAY') && $type != config('task_type.TYPE_WEEK') && $type != config('task_type.TYPE_MONTH')){
            if ($beginDate && $endDate) {
                $begin = date('Y-m-d H:i:s', strtotime($beginDate));
                $end = date('Y-m-d H:i:s', strtotime($endDate));
                $items->whereBetween('DEADLINE', [$begin, $end]);
            }
        }

        // Show expired items
        if (!in_array($type, [config('task_type.TYPE_DONE'), config('task_type.TYPE_SUBSCRIBED'), config('task_type.TYPE_RELATION'), config('task_type.TYPE_PROJECT'), config('task_type.TYPE_TASKLIST')])) {
            $today = date('Y-m-d');
            $items->orWhere(function ($query) use ($today, $forcedUser, $type, $assignee, $filter) {
                $query->where('DEADLINE', '<', $today)
                    ->where('DONE', false)
                    ->where('ACTIVE', true);


                if (($assignee > 0) && ($forcedUser == 0)) {
                    $query->where([
                        'FK_CORE_USER_ASSIGNEE' => $assignee
                    ]);
                }
                if (($forcedUser > 0) && ($type != config('task_type.TYPE_SUBSCRIBED'))) {
                    $query->where(function ($query) use ($forcedUser) {
                        $query->where('FK_CORE_USER_ASSIGNEE', $forcedUser)
                            ->orWhere('FK_CORE_USER_CREATED', $forcedUser);
                    });
                }

                // Apply filter
                if ($filter != '') {
                    $query->where(function ($whereFilter) use ($filter) {
                        $whereFilter->where('SUBJECT', 'like', '%' . $filter . '%');
                        $whereFilter->orWhere('CONTENT', 'like', '%' . $filter . '%');
                        $whereFilter->orWhereHas('project', function ($whereFilter) use ($filter) {
                            $whereFilter->where('DESCRIPTION', 'like', '%' . $filter . '%');
                        });
                    });
                }
            });
        }

        // Apply forced user
        if (($forcedUser > 0) && ($type != config('task_type.TYPE_SUBSCRIBED'))) {
            $items->where(function($query) use ($forcedUser) {
                $query->where('FK_CORE_USER_ASSIGNEE', $forcedUser)
                    ->orWhere('FK_CORE_USER_CREATED', $forcedUser);
            });
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
            ['filter', $filter],
            ['filter_active', $active],
            ['filter_status', $statusTask]
        ]);

        return $bindings;
    }

    public function retrieveTasks(Request $request)
    {
        $type = ( $request->get('TYPE') ?? 0 );
        $active = ( $request->get('ACTIVE') ?? true );
        $status = ( $request->get('STATUS') ?? 0);
        $pid = ( $request->get('PID') ?? 0 );
        $screen = ( $request->get('SCREEN') ?? 0 );
        $assignee = ( $request->get('ASSIGNEE') ?? 0 );
        $category = ( $request->get('CATEGORY') ?? SessionUtils::getSession('ADM_TASK', 'ADM_FILTER_TASK_FILTERS', 0) );
        $filter = ( $request->get('FILTER') ?? '' );
        $page = ( $request->get('PAGE') ?? 0 );
        $beginDate = ( $request->get('BEGINDATE') ?? 0 );
        $endDate = ( $request->get('ENDDATE') ?? 0 );

        $tasksSubs = TaskSubsription::where('ACTIVE', true)
            ->where('FK_CORE_USER', Auth::guard()->user()->ID)
            ->get();

        $view = view('admin.tasks.items')
            ->with('type', $type)
            ->with('screen', $screen)
            ->with('assignee', $assignee)
            ->with('tasksSubs', $tasksSubs);

        $extraBindings = $this->beforeRetrieveTasks($type, $pid, $page, $assignee,$category, $filter, $beginDate, $endDate, $active, $status);
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
        $localeId = config('app.locale_id') ? config('app.locale_id') : config('language.defaultLangID');

        // Get values or create values for categories
        $categories = collect($categoriesInput)->map(function($item) use ($localeId) {
            $dropdownValue = DropdownValue::where('ACTIVE', true)->where('FK_CORE_DROPDOWNTYPE', config('dropdown_type.TYPE_TASK_CATEGORY'))
                ->whereHas('translations', function($filter) use ($localeId, $item) {
                    $filter->where([
                        'FK_CORE_LANGUAGE' => $localeId,
                        'TEXT' => $item->value
                    ]);
                })->first();

            if (!$dropdownValue) {
                // Add new
                $dropdownValue = new DropdownValue([
                    'ACTIVE' => true,
                    'FK_CORE_DROPDOWNTYPE' => config('dropdown_type.TYPE_TASK_CATEGORY')
                ]);
                $dropdownValue->save();

                // Refresh for translation key
                $dropdownValue->refresh();

                // Update translations
                Translation::where('FK_CORE_TRANSLATION_KEY', $dropdownValue->TL_VALUE)
                    ->update(['TEXT' => $item->value]);
            }

            return (object)['id' => ($dropdownValue->ID ?? null)];
        });

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
            // Delete unused categories from task
            Filter::where('FK_TASK', $item->ID)->whereNotIn('FK_CORE_DROPDOWNVALUE', $categories->pluck('id')->toArray())->delete();

            // Add new categories
            foreach ($categories as $category) {
                if (($category->id ?? 0) > 0) {
                    Filter::firstOrCreate([
                        'FK_TASK' => $item->ID,
                        'FK_CORE_DROPDOWNVALUE' => $category->id
                    ]);
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