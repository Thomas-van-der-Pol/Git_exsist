@if(count($errors) > 0)
    <div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
        @foreach($errors as $error)
            <div class="alert alert-solid-danger alert-bold mb-2 mr-4" role="alert">
                <div class="alert-text">
                    {{ $error }}
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="kt-portlet__body kt-portlet__body--fit">
    {{ KJDatatable::create(
        'ADM_BILLCHECK_DETAIL_TABLE',
        array (
            'method' => 'GET',
            'url' => '/admin/invoice/prepare/allDetailDatatable/'.$ids,
            'pagination' => false,
            'sortable' => false,
            'editable' => false,
            'pagesize' => -1,
            'columns' => array(
                array(
                    'field' => 'QUANTITY_FORMATTED',
                    'title' => KJLocalization::translate('Admin - Facturen', 'Aantal', 'Aantal'),
                    'width' => 60
                ),
                array(
                    'field' => 'PERIOD_FORMATTED',
                    'title' => KJLocalization::translate('Admin - Facturen', 'Periode', 'Periode')
                ),
                array(
                    'field' => 'DESCRIPTION',
                    'title' => KJLocalization::translate('Admin - Facturen', 'Omschrijving', 'Omschrijving'),
                ),
                array(
                    'field' => 'PRICE_FORMATTED',
                    'title' => KJLocalization::translate('Admin - Facturen', 'Stukprijs excl', 'Stukprijs excl.')
                ),
                array(
                    'field' => 'PRICE_TOTAL_FORMATTED',
                    'title' => KJLocalization::translate('Admin - Facturen', 'Subtotaal excl', 'Subtotaal excl.')
                ),
                array(
                    'field' => 'PRICE_INCVAT_FORMATTED',
                    'title' => KJLocalization::translate('Admin - Facturen', 'Stukprijs incl', 'Stukprijs incl.')
                ),
                array(
                    'field' => 'PRICE_TOTAL_INCVAT_FORMATTED',
                    'title' => KJLocalization::translate('Admin - Facturen', 'Subtotaal incl', 'Subtotaal incl.')
                ),
            ),
        )
    ) }}
</div>