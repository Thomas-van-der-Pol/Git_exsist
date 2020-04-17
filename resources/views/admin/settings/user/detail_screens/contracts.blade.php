<div class="kt-portlet__body">
    <div class="kt-form kt-form--label-right">
        <div class="row align-items-center">
            <div class="col-auto order-2 order-xl-1">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="form-inline md-form filter-icon">
                            {{ Form::text(
                                'ADM_FILTER_USER_SERVICES_SEARCH',
                                '',
                                array(
                                    'class'         => 'form-control filter',
                                    'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken').'...',
                                    'id'            => 'ADM_FILTER_USER_SERVICES_SEARCH',
                                )
                            ) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col order-2 order-xl-2">
                <a href="javascript:;" id="addContract" class="btn btn-success btn-sm btn-upper pull-right">
                    <i class="fa fa-plus-square"></i>
                    {{ KJLocalization::translate('Admin - Werknemers', 'Contract', 'Contract')}}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="kt-separator m-0"></div>

{{ KJDatatable::create(
    'ADM_USER_CONTRACT_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/settings/user/contract/allByUserDatatable/' . $item->ID,
        'parentid' => $item->ID,
        'editable' => true,
        'editURL' => '/admin/settings/user/contract/detailRendered/',
        'addable' => true,
        'addButton' => '#addContract',
        'saveURL' => '/admin/settings/user/contract',
        'columns' => array(
            array(
                'field' => 'CONTRACTTYPE',
                'title' => KJLocalization::translate('Admin - Werknemers', 'Contracttype', 'Contracttype')
            ),
            array(
                'field' => 'DATE_START_FORMATTED',
                'title' => KJLocalization::translate('Admin - Werknemers', 'Startdatum', 'Startdatum')
            ),
            array(
                'field' => 'END_START_FORMATTED',
                'title' => KJLocalization::translate('Admin - Werknemers', 'Einddatum', 'Einddatum')
            )
        ),
        'filters' => array(
            array(
                'input' => '#ADM_FILTER_USER_CONTRACT_ACTIVE',
                'queryParam' => 'ACTIVE',
                'default' => 1
            )
        )
    )
) }}