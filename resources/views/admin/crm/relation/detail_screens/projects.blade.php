<div class="kt-portlet__body">
    <div class="kt-form kt-form--label-right">
        <div class="row align-items-center">
            <div class="col-2">
                <div class="kt-form__group kt-form__group--inline">
                    <div class="kt-form__label">
                        {{ Form::label('ADM_FILTER_PROJECT_STATUS_LABEL', KJLocalization::translate('Algemeen', 'Status', 'Status'). ':') }}
                    </div>
                    <div class="kt-form__control">
                        {{ Form::select(
                            'ADM_FILTER_PROJECT_STATUS',
                            $status,
                            1,
                            [
                                'class' => 'form-control filter kt-bootstrap-select',
                                'id'    => 'ADM_FILTER_PROJECT_STATUS',
                            ]
                        ) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="kt-separator m-0"></div>

{{ KJDatatable::create(
    'ADM_RELATION_PROJECT_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/crm/project/allByRelationDatatable/' . $item->ID,
        'parentid' => $item->ID,
        'columns' => array(
            array(
                'field' => 'ID',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Nr', 'Nr.'),
                'width' => 20
            ),
            array(
                'field' => 'DESCRIPTION',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Omschrijving', 'Omschrijving')
            ),
            array(
                'field' => 'DEADLINE_FORMATTED',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Deadline', 'Deadline')
            ),
            array(
                'field' => 'WORKFLOWSTATE',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Status', 'Status')
            ),
            array(
                'field' => 'TOTAL_PRICE',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Totaalbedrag', 'Totaalbedrag')
            )
        ),
        'filters' => [
            [
                'input' => '#ADM_FILTER_PROJECT_STATUS',
                'queryParam' => 'ACTIVE',
                'default' => 1
            ]
        ],
        'customEditButtons' => array(
            'end' => [
                [
                    'HTML'  => '<a href="/admin/project/detail/{idField}" data-id="{idField}" class="btn btn-bold btn-label-brand btn-sm btn-icon visiteProject" title="' . KJLocalization::translate("Algemeen", "Bekijk project", "Bekijk project") . '" ><i class="la la-eye""></i></a>'
                ]
            ]
        )
    )
) }}