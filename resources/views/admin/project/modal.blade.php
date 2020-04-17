<div class="kt-form kt-form--label-right">
    <div class="row align-items-center">
        <div class="col-auto order-2 order-xl-1">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="form-inline md-form filter-icon">
                        {{ Form::text(
                            'ADM_PROJECT_FILTER',
                            '',
                            [
                                'class'         => 'form-control filter',
                                'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken').'...',
                                'id'            => 'ADM_PROJECT_FILTER',
                            ]
                        ) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{ KJDatatable::create(
    'ADM_PROJECT_MODAL_TABLE',
    [
        'method' => 'GET',
        'url' => '/admin/project/modal/allByModalProjectDatatable',
        'searchinput' => '#ADM_PROJECT_FILTER',
        'selectable' => true,
        'columns' => [
            array(
                'field' => 'DESCRIPTION',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Omschrijving', 'Omschrijving')
            ),
            array(
                'field' => 'RELATION',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Relatie', 'Relatie')
            )
        ]
    ]
) }}