<div class="kt-portlet__body">
    <div class="kt-form kt-form--label-right">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="form-inline md-form filter-icon">
                            {{ Form::text(
                                'ADM_CONTACT_FILTER_SEARCH',
                                '',
                                [
                                    'class'         => 'form-control filter',
                                    'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken').'...',
                                    'id'            => 'ADM_CONTACT_FILTER_SEARCH',
                                ]
                            ) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-2">
                <div class="kt-form__group kt-form__group--inline">
                    <div class="kt-form__label">
                        {{ Form::label('ADM_FILTER_CONTACT_LABEL_STATUS', KJLocalization::translate('Algemeen', 'Status', 'Status'). ':') }}
                    </div>
                    <div class="kt-form__control">
                        {{ Form::select(
                            'ADM_FILTER_CONTACT_STATUS',
                            $status,
                            1,
                            [
                                'class' => 'form-control filter kt-bootstrap-select hasSessionState',
                                'id'    => 'ADM_FILTER_CONTACT_STATUS',
                            ]
                        ) }}
                    </div>
                </div>
            </div>

            <div class="col order-2 order-xl-2">
                <a href="javascript:;" id="addContact" class="btn btn-success btn-sm btn-upper pull-right">
                    <i class="fa fa-plus-square"></i>
                    {{ KJLocalization::translate('Admin - CRM', 'Contact', 'Contact')}}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="kt-separator m-0"></div>

{{ KJDatatable::create(
    'ADM_RELATION_CONTACT_TABLE',
    [
        'method' => 'GET',
        'url' => '/admin/crm/relation/contact/allByRelationDatatable/' . $item->ID,
        'parentid' => $item->ID,
        'editable' => true,
        'editURL' => '/admin/crm/relation/contact/detailRendered/',
        'addable' => true,
        'addButton' => '#addContact',
        'saveURL' => '/admin/crm/relation/contact',
        'searchinput' => '#ADM_CONTACT_FILTER_SEARCH',
        'columns' => [
            [
                'field' => 'FULLNAME',
                'title' => KJLocalization::translate('Admin - CRM', 'Naam', 'Naam')
            ],
            [
                'field' => 'EMAILADDRESS',
                'title' => KJLocalization::translate('Admin - CRM', 'E-mail', 'E-mail')
            ],
            [
                'field' => 'PHONENUMBER',
                'title' => KJLocalization::translate('Admin - CRM', 'Telefoon', 'Telefoon')
            ]
        ],
        'filters' => [
            [
                'input' => '#ADM_FILTER_CONTACT_STATUS',
                'queryParam' => 'ACTIVE',
                'default' => 1
            ]
        ]
    ]
) }}