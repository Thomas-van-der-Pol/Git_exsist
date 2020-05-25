<?php

namespace App\Http\Controllers\Admin\Project;

use App\Libraries\Core\DropdownvalueUtils;
use App\Models\Admin\Core\Label;
use App\Models\Admin\Project\Project;
use App\Models\Core\WorkflowState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use KJ\Core\controllers\AdminBaseController;
use KJ\Core\libraries\SessionUtils;
use KJLocalization;

class ProjectController extends AdminBaseController {

    protected $model = 'App\Models\Admin\Project\Project';

    protected $mainViewName = 'admin.project.main';

    protected $detailScreenOverviewFolder = 'admin.project.overview_screens';

    protected $allColumns = ['ID', 'ACTIVE', 'TS_CREATED', 'TS_LASTMODIFIED_STATE', 'DESCRIPTION', 'FK_CRM_RELATION_REFERRER', 'FK_CRM_RELATION_EMPLOYER', 'FK_CRM_CONTACT_EMPLOYEE', 'FK_CORE_DROPDOWNVALUE_PROJECTTYPE', 'FK_CORE_WORKFLOWSTATE', 'START_DATE'];

    protected $joinClause = [
        [
            'TABLE' => 'CRM_RELATION',
            'ALIAS' => 'REFERRER',
            'PRIMARY_FIELD' => 'ID',
            'FOREIGN_FIELD' => 'PROJECT.FK_CRM_RELATION_REFERRER',
        ],
        [
            'TABLE' => 'CRM_RELATION',
            'ALIAS' => 'EMPLOYER',
            'PRIMARY_FIELD' => 'ID',
            'FOREIGN_FIELD' => 'PROJECT.FK_CRM_RELATION_EMPLOYER',
        ],
        [
            'TABLE' => 'CRM_CONTACT',
            'ALIAS' => 'EMPLOYEE',
            'PRIMARY_FIELD' => 'ID',
            'FOREIGN_FIELD' => 'PROJECT.FK_CRM_CONTACT_EMPLOYEE',
        ]
    ];

    protected $datatableDefaultSort = array(
        [
            'field' => 'TS_CREATED',
            'sort'  => 'DESC'
        ]
    );

    protected $detailScreenFolder = 'admin.project.detail_screens';

    protected $detailViewName = 'admin.project.detail';

    protected $saveUnsetValues = [
        'REFERRER_NAME',
        'EMPLOYER_NAME',
        'EMPLOYEE_NAME',
        'COMPENSATION_PERCENTAGE_READ',
        'USER_CREATED',
        'DATE_CREATED',
    ];

    protected function authorizeRequest($method, $parameters)
    {
        return Auth::guard()->user()->hasPermission(config('permission.DOSSIERS'));
    }

    protected function beforeIndex()
    {
        $status = DropdownvalueUtils::getStatusDropdown(false);

        $project_all = [0 => KJLocalization::translate('Admin - Dossiers', 'Alle dossiers', 'Alle dossiers')];

        $project_types = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_PROJECTTYPE'), false);
        $project_types = $project_all + $project_types;

        $bindings = array(
            ['status', $status],
            ['project_types', $project_types]
        );

        return $bindings;
    }

    public function modal()
    {
        $view = view('admin.project.modal');

        return response()->json([
            'viewDetail' => $view->render()
        ]);
    }

    protected function getSortField($sortField)
    {
        if ($sortField === 'LASTMODIFIED_STATE_FORMATTED') {
            return 'TS_LASTMODIFIED_STATE';
        } else if ($sortField === 'START_DATE_FORMATTED') {
            return 'START_DATE';
        } else if ($sortField === 'CREATED_DATE_FORMATTED') {
            return 'TS_CREATED';
        } else {
            return parent::getSortField($sortField);
        }
    }

    public function allByWorkflowDatatable(Request $request, int $TYPE_ID, int $ID)
    {
        $this->whereClause = [];
        $this->whereRawClause = [];

        if ($TYPE_ID > 0) {
            $this->whereClause = array_merge($this->whereClause, [['FK_CORE_DROPDOWNVALUE_PROJECTTYPE', $TYPE_ID]]);
        }

        if ($ID > 0) {
            $this->whereClause = array_merge($this->whereClause, [['FK_CORE_WORKFLOWSTATE', $ID]]);
        }

        $this->datatableFilter = [
            ['ACTIVE', array(
                'param' => 'ACTIVE',
                'default' => SessionUtils::getSession('ADM_PROJECT', 'ADM_FILTER_PROJECT_STATUS', 1)
            )],
            ['ID, DESCRIPTION, REFERRER.NAME, EMPLOYER.NAME, EMPLOYEE.FULLNAME', array(
                'param' => 'ADM_PROJECT_FILTER_SEARCH',
                'operation' => 'like',
                'default' => SessionUtils::getSession('ADM_PROJECT', 'ADM_PROJECT_FILTER_SEARCH', ''),
                'keywords' => true
            )]
        ];

        // Show projects with status done
        $show_done = ((int)($request->query('query')['SHOW_DONE'] ?? 0) == 1);
        if ($show_done == false) {
            $this->whereNotInClause = [
                ['FK_CORE_WORKFLOWSTATE', [config('workflowstate.PROJECT_DONE')]]
            ];
        }

        return parent::allDatatable($request);
    }

    public function allByModalProjectDatatable(Request $request)
    {
        $this->whereClause = array(
            ['ACTIVE', true],
            ['INVOICING_COMPLETE', false]
        );

        $this->datatableFilter = [
            ['ID, DESCRIPTION, REFERRER.NAME, EMPLOYER.NAME, EMPLOYEE.FULLNAME', array(
                'param' => 'ADM_PROJECT_FILTER',
                'operation' => 'like',
                'default' => ''
            )]
        ];

        return parent::allDatatable($request);
    }

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('REFERRER', function(Project $project) {
                return $project->referrer ? $project->referrer->title : '';
            })
            ->addColumn('EMPLOYER', function(Project $project) {
                return $project->employer ? $project->employer->title : '';
            })
            ->addColumn('EMPLOYEE', function(Project $project) {
                return $project->employee ? $project->employee->title : '';
            })
            ->addColumn('PROJECTTYPE', function(Project $project) {
                return $project->type ? $project->type->value : '';
            })
            ->addColumn('WORKFLOWSTATE', function(Project $project) {
                $html = '<div class="progress" style="height: 16px; width: 100%">
                            <div class="progress-bar kt-bg-brand" role="progressbar" style="width: '.$project->progress().'%;">
                                ' . ($project->workflowstate ? $project->workflowstate->DESCRIPTION : "") . '
                            </div>
                        </div>' . $project->progress() . '%';

                return new HtmlString($html);
            })
            ->addColumn('START_DATE_FORMATTED', function(Project $project) {
                return $project->getStartDateFormattedAttribute();
            })
            ->addColumn('CREATED_DATE_FORMATTED', function(Project $project) {
                return $project->getCreatedDateFormattedAttribute();
            })
            ->addColumn('LASTMODIFIED_STATE_FORMATTED', function(Project $project) {
                return $project->getLastModifiedStateFormattedAttribute();
            });
    }

    public function detailScreenOverview(Request $request) {
        if ($this->detailScreenOverviewFolder == '') {
            abort(400, 'Geen detail screen overview folder opgegeven! Vul variabele detailScreenOverviewFolder.');
        }

        $type = $request->get('type');

        $workflowStates = WorkflowState::where(['ACTIVE' => true, 'FK_CORE_WORKFLOWSTATETYPE' => config('workflowstate_type.TYPE_PROJECT')])
            ->orderBy('SEQUENCE')
            ->get();

        $workflowStates->prepend((object)[
            'ID' => 0,
            'DESCRIPTION' => KJLocalization::translate('Admin - Dossiers', 'Alle dossiers', 'Alle dossiers')
        ]);

        $view = view($this->detailScreenOverviewFolder.'.project_base')
            ->with('type', $type)
            ->with('workflowStates', $workflowStates);

        return response()->json([
            'success' => true,
            'type' => $type,
            'view' => $view->render()
        ]);
    }

    public function beforeDetailScreen(int $id, $item, $screen)
    {
        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        $bindings = [];

        switch ($screen) {
            case 'default':
                $labels = Label::where('ACTIVE', true);
                $default_label = null;
                if ($labels->count() == 1) {
                    $default_label = $labels->first()->ID;
                }
                $labels = $none + $labels->pluck('DESCRIPTION', 'ID')->toArray();

                $project_types = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_PROJECTTYPE'));

                $contacts_referrer = [];
                $contacts_employer = [];
                if ($item) {
                    if ($item->referrer) {
                        $contacts_referrer = $none + $item->referrer->contacts()->where('ACTIVE', true)->pluck('FULLNAME', 'ID')->toArray();
                    }

                    if ($item->employer) {
                        $contacts_employer = $none + $item->employer->contacts()->where('ACTIVE', true)->pluck('FULLNAME', 'ID')->toArray();
                    }
                }

                $previousWorkflowstate = null;
                $nextWorkflowstate = null;
                if ($item && $item->workflowstate) {
                    $previousWorkflowstate = WorkflowState::where(['ACTIVE' => true, 'FK_CORE_WORKFLOWSTATETYPE' => config('workflowstate_type.TYPE_PROJECT')])
                        ->where('SEQUENCE', '<', $item->workflowstate->SEQUENCE)
                        ->orderBy('SEQUENCE', 'desc')
                        ->first();

                    $nextWorkflowstate = WorkflowState::where(['ACTIVE' => true, 'FK_CORE_WORKFLOWSTATETYPE' => config('workflowstate_type.TYPE_PROJECT')])
                        ->where('SEQUENCE', '>', $item->workflowstate->SEQUENCE)
                        ->orderBy('SEQUENCE', 'asc')
                        ->first();
                }

                $bindings = array_merge($bindings, [
                    ['labels', $labels],
                    ['default_label', $default_label],
                    ['project_types', $project_types],
                    ['previousWorkflowstate', $previousWorkflowstate],
                    ['nextWorkflowstate', $nextWorkflowstate],
                    ['contacts_referrer', $contacts_referrer],
                    ['contacts_employer', $contacts_employer],
                ]);
                break;

            case 'invoices':
                $advance_type_ori = [
                    1 => KJLocalization::translate('Admin - Facturen', 'Percentage', 'Percentage'),
                    2 => KJLocalization::translate('Admin - Facturen', 'Vast bedrag', 'Vast bedrag')
                ];
                $advance_type = $none + $advance_type_ori;

                $bindings = array_merge($bindings, [
                    ['advance_type', $advance_type],
                ]);
                break;
        }

        return $bindings;
    }

    protected function afterSave($item, $originalItem, Request $request, &$response)
    {
        if ($request->get('ID') == $this->newRecordID) {
            $item->CREATE_FK_CORE_USER = Auth::guard()->user()->ID;

            // Reset workflowstate
            $nextWorkflowstate = WorkflowState::where(['ACTIVE' => true, 'FK_CORE_WORKFLOWSTATETYPE' => config('workflowstate_type.TYPE_PROJECT')])
                ->orderBy('SEQUENCE', 'asc')
                ->first();

            $item->FK_CORE_WORKFLOWSTATE = ($nextWorkflowstate->ID ?? null);

            // Store
            $item->save();
        }
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