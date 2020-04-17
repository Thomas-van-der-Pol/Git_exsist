<div class="kt-form kt-form--label-right">
    <div class="row align-items-center">
        <div class="col-xl-12 order-2 order-xl-1">
            <div class="row align-items-center">
                <div class="col-md-6 kt-margin-b-20-tablet-and-mobile">
                    <div class="kt-input-icon kt-input-icon--left">
                        {{ Form::text(
                            'ADM_CRM_CONTACT_FILTER_SEARCH',
                            \KJ\Core\libraries\SessionUtils::getSession('ADM_CRM', 'ADM_CRM_CONTACT_FILTER_SEARCH', ''),
                            array(
                                'class'         => 'form-control filter hasSessionState',
                                'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken') . '..',
                                'id'            => 'ADM_CRM_CONTACT_FILTER_SEARCH',
                                'data-module'   => 'ADM_CRM',
                                'data-key'      => 'ADM_CRM_CONTACT_FILTER_SEARCH'
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
    'ADM_CRM_CONTACT_MODAL_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/crm/contact/allDatatable',
        'pagination' => true,
        'sortable' => true,
        'searchinput' => '#ADM_CRM_CONTACT_FILTER_SEARCH',
        'selectable' => true,
        'columns' => array(
            array(
                'field' => 'RELATION',
                'title' => KJLocalization::translate('Admin - CRM', 'Relatie', 'Relatie')
            ),
            array(
                'field' => 'FULLNAME',
                'title' => KJLocalization::translate('Admin - CRM', 'Naam', 'Naam')
            ),
            array(
                'field' => 'EMAILADDRESS',
                'title' => KJLocalization::translate('Admin - CRM', 'E-mailadres', 'E-mailadres')
            ),
            array(
                'field' => 'PHONENUMBER',
                'title' => KJLocalization::translate('Admin - CRM', 'Telefoonnummer', 'Telefoonnummer')
            ),
        )
    )
) }}