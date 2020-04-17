@php
    $editButton = '';

    if(($item ? $item->FK_CORE_WORKFLOWSTATE : config('workflowstate.INVOICE_CONCEPT')) != config('workflowstate.INVOICE_CONCEPT'))
    {
        // Definitieve factuur
        $disabledInvoiceLines = true;
    }
    else
    {
        // Conceptfactuur
        $disabledInvoiceLines = !($item->MANUAL ?? true);
        $editButton = '<a href="javascript:;" data-id="{idField}" class="btn btn-bold btn-label-danger btn-sm btn-icon deleteInvoiceLine" title="' . KJLocalization::translate("Algemeen", "Verwijderen", "Verwijderen") . '" ><i class="la la-close""></i></a>';
    }
@endphp

@if($disabledInvoiceLines === false)
    <div class="kt-portlet__body">
        <div class="kt-form kt-form--label-right">
            <div class="row align-items-center">
                <div class="col order-2 order-xl-2">
                    <a href="javascript:;" id="newInvoiceItem" class="btn btn-success btn-sm btn-upper pull-right" data-id="{{ $item->ID }}" data-type="0">
                        <i class="fa fa-plus-square"></i>
                        {{ KJLocalization::translate('Admin - Facturen', 'Factuurregel', 'Factuurregel')}}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="kt-separator m-0"></div>
@endif

{{ KJDatatable::create(
    'ADM_INVOICE_LINES_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/invoice/line/allByInvoiceDatatable/' . $item->ID,
        'parentid' => $item->ID,
        'pagination' => true,
        'sortable' => false,
        'editable' => ($disabledInvoiceLines === false),
        'editURL' => '/admin/invoice/line/detailRendered/',
        'addable' => ($disabledInvoiceLines === false),
        'addButton' => '#newInvoiceItem',
        'saveURL' => '/admin/invoice/line',
        'columns' => array(
            array(
                'field' => 'QUANTITY_FORMATTED',
                'title' => KJLocalization::translate("Admin - Facturen", "Aantal", "Aantal"),
                'width' => 50
            ),
            array(
                'field' => 'PERIOD_FORMATTED',
                'title' => KJLocalization::translate("Admin - Facturen", "Periode", "Periode"),
                'width' => 100
            ),
            array(
                'field' => 'DESCRIPTION',
                'title' => KJLocalization::translate("Admin - Facturen", "Omschrijving", "Omschrijving"),
            ),
            array(
                'field' => 'PRICE_FORMATTED',
                'title' => KJLocalization::translate("Admin - Facturen", "Stukprijs excl", "Stukprijs excl.")
            ),
            array(
                'field' => 'PRICETOTAL_FORMATTED',
                'title' => KJLocalization::translate("Admin - Facturen", "Subtotaal excl", "Subtotaal excl.")
            ),
            array(
                'field' => 'PRICETOTAL_INCVAT_FORMATTED',
                'title' => KJLocalization::translate("Admin - Facturen", "Subtotaal incl", "Subtotaal incl.")
            ),
            array(
                'field' => 'LEDGER',
                'title' => KJLocalization::translate("Admin - Facturen", "Grootboekrekening", "Grootboekrekening"),
                'width' => 200
            ),
            array(
                'field' => 'VAT',
                'title' => KJLocalization::translate("Admin - Facturen", "Btw", "Btw")
            )
        ),
        'customEditButtons' => array(
            'end' => [
                [
                    'HTML'  => $editButton
                ]
            ]
        )
    )
) }}