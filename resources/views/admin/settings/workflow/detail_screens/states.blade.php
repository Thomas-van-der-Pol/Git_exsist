<div class="kt-portlet__body">
    <div class="kt-form kt-form--label-right">
        <div class="row align-items-center">
            <div class="col order-2 order-xl-2">
                <a href="javascript:;" id="addState" class="btn btn-success btn-sm btn-upper pull-right">
                    <i class="fa fa-plus-square"></i>
                    {{ KJLocalization::translate('Admin - Workflows', 'Status', 'Status')}}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="kt-separator m-0"></div>

{{ KJDatatable::create(
    'ADM_WORKFLOW_STATE_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/settings/workflow/state/allByTypeDatatable/' . $item->ID,
        'parentid' => $item->ID,
        'editable' => true,
        'editURL' => '/admin/settings/workflow/state/detailRendered/',
        'addable' => true,
        'addButton' => '#addState',
        'saveURL' => '/admin/settings/workflow/state',
        'orderable' => true,
        'sequenceField' => 'SEQUENCE',
        'columns' => array(
            array(
                'field' => 'DESCRIPTION',
                'title' => KJLocalization::translate('Admin - Workflows', 'Omschrijving', 'Omschrijving')
            ),
            array(
                'field' => 'ACTION_DESCRIPTION',
                'title' => KJLocalization::translate('Admin - Workflows', 'Actie omschrijving', 'Actie omschrijving')
            )
        ),
        'customEditButtons' => array(
            'end' => [
                [
                    'HTML'  => '<a href="javascript:;" data-id="{idField}" class="btn btn-bold btn-label-danger btn-sm btn-icon deleteState" title="' . KJLocalization::translate("Algemeen", "Verwijderen", "Verwijderen") . '" ><i class="la la-close""></i></a>'
                ]
            ]
        )
    )
) }}