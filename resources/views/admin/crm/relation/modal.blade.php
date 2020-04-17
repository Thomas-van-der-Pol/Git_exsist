<div class="kt-form kt-form--label-right">
    <div class="row align-items-center">
        <div class="col-xl-12 order-2 order-xl-1">
            <div class="row align-items-center">
                <div class="col-md-6 kt-margin-b-20-tablet-and-mobile">
                    <div class="kt-input-icon kt-input-icon--left">
                        {{ Form::text(
                            'ADM_RELATION_FILTER_SEARCH',
                            \KJ\Core\libraries\SessionUtils::getSession('ADM_RELATION', 'ADM_RELATION_FILTER_SEARCH', ''),
                            array(
                                'class'         => 'form-control hasSessionState',
                                'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken') . '..',
                                'id'            => 'ADM_RELATION_FILTER_SEARCH',
                                'data-module'   => 'ADM_RELATION',
                                'data-key'      => 'ADM_RELATION_FILTER_SEARCH'
                            )
                        ) }}
                        <span class="kt-input-icon__icon kt-input-icon__icon--left">
                            <span><i class="la la-search"></i></span>
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{ KJDatatable::create(
    'ADM_RELATION_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/crm/relation/allDatatable',
        'pagination' => true,
        'sortable' => true,
        'searchinput' => '#ADM_RELATION_FILTER_SEARCH',
        'selectable' => true,
        'columns' => array(
            array(
                'field' => 'NAME',
                'title' => KJLocalization::translate('Admin - CRM', 'Naam', 'Naam')
            )
        )
    )
) }}