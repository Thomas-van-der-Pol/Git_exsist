<div class="kt-form kt-form--label-right">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="kt-form__group kt-form__group--inline">
                <div class="kt-form__label">
                    {{ Form::label('ADM_DROPDOWNVALUE_FILTER_ACTIVE', KJLocalization::translate('Algemeen', 'Status', 'Status'). ':') }}
                </div>
                <div class="kt-form__control">
                    {{ Form::select(
                        'ADM_DROPDOWNVALUE_FILTER_ACTIVE',
                        $status,
                        1,
                        [
                            'class' => 'form-control filter kt-bootstrap-select'
                        ]
                    ) }}
                </div>
            </div>
        </div>

        <div class="col order-2 order-xl-2">
            <a href="javascript:;" id="addDropdownValue" class="btn btn-success btn-sm btn-upper pull-right">
                <i class="fa fa-plus-square"></i>
                {{ KJLocalization::translate('Algemeen', 'Waarde', 'Waarde') }}
            </a>
        </div>
    </div>
</div>

{{ KJDatatable::create(
    'ADM_DROPDOWNVALUE_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/dropdownvalue/allByTypeDatatable/' . app('request')->input('typeid'),
        'parentid' => (int)app('request')->input('typeid'),
        'pagination' => true,
        'editable' => true,
        'editURL' => '/admin/dropdownvalue/detailRendered/',
        'addable' => true,
        'addButton' => '#addDropdownValue',
        'saveURL' => '/admin/dropdownvalue',
        'columns' => array(
            array(
                'field' => 'SEQUENCE',
                'title' => KJLocalization::translate('Algemeen', 'Volgorde', 'Volgorde') ,
                'width' => 70
            ),
            array(
                'field' => 'VALUE',
                'title' => KJLocalization::translate('Algemeen', 'Waarde', 'Waarde')
            )
        ),
        'filters' => array(
            array(
                'input' => '#ADM_DROPDOWNVALUE_FILTER_ACTIVE',
                'queryParam' => 'ACTIVE',
                'default' => 1
            )
        )
    )
) }}