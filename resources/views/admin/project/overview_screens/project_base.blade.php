<div class="row">
    <div class="col-lg-12">
        <div class="kt-portlet kt-portlet--tabs kt-portlet--inline">
            <div class="kt-portlet__head {{ (($workflowStates->count() > 1) ? '' : 'kt-hidden') }}">
                <div class="kt-portlet__head-toolbar">
                    <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-brand nav-tabs-line-2x nav-tabs-line-right nav-tabs-bold" role="tablist">
                        @foreach($workflowStates as $workflowState)
                            <li class="nav-item">
                                <a class="nav-link {{ ($workflowState->ID == 0) ? 'active' : '' }}" data-toggle="tab" data-state="{{ $workflowState->ID }}" href="#workflow_state_{{ $type }}_{{ $workflowState->ID }}_content" role="tab" aria-selected="true">
                                    {{ $workflowState->DESCRIPTION }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="kt-portlet__body kt-portlet__body--fit">
                <div class="tab-content">
                    @foreach($workflowStates as $workflowState)
                        <div class="tab-pane {{ ($workflowState->ID == 0) ? 'active' : '' }}" id="workflow_state_{{ $type }}_{{ $workflowState->ID }}_content" role="tabpanel">
                            {{ KJDatatable::create(
                                'ADM_PROJECT_TABLE_' . $type . '_' . $workflowState->ID,
                                array (
                                    'method' => 'GET',
                                    'url' => '/admin/project/allByWorkflowDatatable/' . $type . '/' . $workflowState->ID,
                                    'editable' => true,
                                    'editinline' => false,
                                    'editURL' => \KJ\Localization\libraries\LanguageUtils::getUrl('admin/project/detail/'),
                                    'addable' => false,
                                    'pagination' => true,
                                    'sortable' => true,
                                    'searchinput' => '#ADM_PROJECT_FILTER_SEARCH',
                                    'columns' => array(
                                        array(
                                            'field' => 'DESCRIPTION',
                                            'title' => KJLocalization::translate('Admin - Dossiers', 'Dossiernaam', 'Dossiernaam')
                                        ),
                                        array(
                                            'field' => 'WORKFLOWSTATE',
                                            'title' => KJLocalization::translate('Admin - Dossiers', 'Status', 'Status'),
                                            'sortable' => false
                                        ),
                                        array(
                                            'field' => 'LASTMODIFIED_STATE_FORMATTED',
                                            'title' => KJLocalization::translate('Admin - Dossiers', 'Status sinds', 'Status sinds')
                                        ),
                                        array(
                                            'field' => 'START_DATE_FORMATTED',
                                            'title' => KJLocalization::translate('Admin - Dossiers', 'Eerste ziektedag', 'Eerste ziektedag')
                                        ),
                                        array(
                                            'field' => 'CREATED_DATE_FORMATTED',
                                            'title' => KJLocalization::translate('Admin - Dossiers', 'Aanmaakdatum', 'Aanmaakdatum')
                                        )
                                    ),
                                    'filters' => array(
                                        array(
                                            'input' => '#ADM_FILTER_PROJECT_STATUS',
                                            'queryParam' => 'ACTIVE',
                                            'default' => \KJ\Core\libraries\SessionUtils::getSession('ADM_PROJECT', 'ADM_FILTER_PROJECT_STATUS', 1)
                                        ),
                                        array(
                                            'input' => '#ADM_FILTER_PROJECT_SHOW_DONE',
                                            'queryParam' => 'SHOW_DONE',
                                            'default' => false
                                        )
                                    )
                                )
                            ) }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>