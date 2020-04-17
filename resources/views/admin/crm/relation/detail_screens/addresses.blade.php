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
                                array(
                                    'class'         => 'form-control filter',
                                    'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken').'...',
                                    'id'            => 'ADM_CONTACT_FILTER_SEARCH',
                                )
                            ) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col order-2 order-xl-2">
                <a href="javascript:;" id="addAddress" class="btn btn-success btn-sm btn-upper pull-right">
                    <i class="fa fa-plus-square"></i>
                    {{ KJLocalization::translate('Admin - CRM', 'Adres', 'Adres')}}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="kt-separator m-0"></div>

{{ KJDatatable::create(
    'ADM_RELATION_ADDRESS_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/crm/address/allByRelationDatatable/' . $item->ID,
        'parentid' => $item->ID,
        'editable' => true,
        'editURL' => '/admin/crm/address/detailRendered/',
        'addable' => true,
        'addButton' => '#addAddress',
        'saveURL' => '/admin/crm/address',
        'columns' => array(
            array(
                'field' => 'ADDRESS_TYPE',
                'title' => KJLocalization::translate('Admin - CRM', 'Adres type', 'Adres type'),
                'width' => 100
            ),
            array(
                'field' => 'FULL_ADDRESS',
                'title' => KJLocalization::translate('Admin - CRM', 'Adres', 'Adres')
            )
        ),
        'customEditButtons' => array(
            'end' => [
                [
                    'HTML'  => '<a href="#" data-id="{idField}" class="btn btn-bold btn-label-brand btn-sm btn-icon copyAddress" title="' . KJLocalization::translate("Global", "Kopieren", "Kopieren") . '" ><i class="la la-copy""></i></a>'
                ],
                [
                    'HTML' => '<a href="javascript:;" data-id="{idField}" class="btn btn-bold btn-label-danger btn-sm btn-icon deleteAddress" title="'.KJLocalization::translate('Algemeen', 'Verwijderen', 'Verwijderen').'" ><i class="la la-close""></i></a>'
                ]
            ]
        ),
        'filters' => array(
            array(
                'input' => '#ADM_FILTER_ADRES_STATUS',
                'queryParam' => 'ACTIVE',
                'default' => \KJ\Core\libraries\SessionUtils::getSession('ADM_RELATION', 'ADM_FILTER_ADRES_STATUS', 1)
            )
        )
    )
) }}