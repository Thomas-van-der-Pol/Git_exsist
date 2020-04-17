@if($item->FK_PROJECT_INVOICE_TYPE == config('project_invoice_type.TYPE_FIXED_PRICE'))
    <div class="kt-portlet__body">
        <div class="kt-form kt-form--label-right">
            <div class="row align-items-center">
                <div class="col order-1 order-xl-1">
                    <a href="javascript:;" id="addAdvanceInvoice" class="btn btn-success btn-sm btn-upper pull-right">
                        <i class="fa fa-plus-square"></i>
                        {{ KJLocalization::translate('Admin - Facturen', 'Voorschotfactuur', 'Voorschotfactuur')}}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="kt-separator m-0"></div>
@endif

@php
    $editable = false;
    if(Auth::guard()->user()->hasPermission(config('permission.FACTURATIE'))) {
        $editable = true;
    }
@endphp

{{ KJDatatable::create(
    'ADM_PROJECT_INVOICE_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/project/invoice/allByProjectDatatable/' . $item->ID,
        'pagination' => true,
        'sortable' => false,
        'editable' => $editable,
        'editinline' => false,
        'pagesize' => 50,
        'editURL' => '/admin/invoice/detail/',
        'columns' => array(
            array(
                'field' => 'NUMBER',
                'title' => KJLocalization::translate("Admin - Facturen", "Factuurnummer", "Factuurnummer")
            ),
            array(
                'field' => 'DATE_FORMATTED',
                'title' => KJLocalization::translate("Admin - Facturen", "Factuurdatum", "Factuurdatum")
            ),
            array(
                'field' => 'TOTAL_PRICE',
                'title' => KJLocalization::translate("Admin - Facturen", "Totaal excl", "Totaal excl.")
            ),
            array(
                'field' => 'TOTAL_PRICE_INCL',
                'title' => KJLocalization::translate("Admin - Facturen", "Totaal incl", "Totaal incl.")
            ),
            array(
                'field' => 'PAID_FORMATTED',
                'title' => KJLocalization::translate("Admin - Facturen", "Betaald", "Betaald")
            ),
            array(
                'field' => 'EXPIRATION_DATE_FORMATTED',
                'title' => KJLocalization::translate("Admin - Facturen", "Vervaldatum", "Vervaldatum")
            ),
            array(
                'field' => 'DAYS_FORMATTED',
                'title' => KJLocalization::translate("Admin - Facturen", "Dgn", "Dgn")
            ),
            array(
                'field' => 'ADVANCE_FORMATTED',
                'title' => KJLocalization::translate("Admin - Facturen", "Voorschot", "Voorschot")
            )
        )
    )
) }}

{{-- Advance invoice pop-up --}}
<div class="modal fade" id="advance_invoice_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{ KJLocalization::translate('Admin - Facturen', 'Voorschotfactuur aanmaken', 'Voorschotfactuur aanmaken')}}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                {{ Form::open(array(
                    'method' => 'post',
                    'id' => 'advance_invoice',
                    'class' => 'kt-form',
                    'novalidate'
                )) }}
                <div class="kt-portlet__body">
                    {{ Form::hidden('ID', $item->ID) }}
                    {{ Form::hidden('MAX_AMOUNT', $item->maxInvoiceAmountDecimal()) }}

                    <div class="alert alert-solid-danger alert-bold advance_error" role="alert" style="display: none;">
                        <div class="alert-text">{{ KJLocalization::translate('Admin - Facturen', 'Foutmelding voorschotfactuur bedrag', 'Het ingevoerde bedrag/percentage is groter dan het maximaal te factureren bedrag!') }}</div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <h5>{{ KJLocalization::translate('Admin - Facturen', 'Maximaal bedrag te factureren', 'Maximaal bedrag te factureren') }} <span class="kt-font-bolder kt-font-brand">{{ $item->maxInvoiceAmountFormatted() }}</span></h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <h6 class="my-3">{{ KJLocalization::translate('Admin - Facturen', 'Voorschotfactuur instellen', 'Voorschotfactuur instellen') }}</h6>
                        </div>
                    </div>

                    {{ KJField::select('ADVANCE_TYPE', KJLocalization::translate('Admin - Facturen', 'Type', 'Type'), $advance_type, '', true, 0, ['data-screen-mode' => 'read, edit']) }}

                    <div class="advance_show_at_type" data-type="1" style="display: none;">
                        {{ KJField::number('ADVANCE_PERCENTAGE', '', '', true, [], [
                            'right' => [
                                ['type' => 'text', 'caption' => '%']]
                            ]
                        ) }}
                    </div>

                    <div class="advance_show_at_type" data-type="2" style="display: none;">
                        {{ KJField::number('ADVANCE_AMOUNT', '', '', true, [], [
                            'right' => [
                                ['type' => 'text', 'caption' => '&euro;']]
                            ]
                        ) }}
                    </div>

                    <div class="row">
                        <div class="col">
                            <h6 class="my-3">{{ KJLocalization::translate('Admin - Facturen', 'Samenvatting', 'Samenvatting') }}</h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <p>{{ KJLocalization::translate('Admin - Facturen', 'Bedrag excl', 'Bedrag excl.') }} <span class="kt-font-bolder kt-font-brand" id="advance_summary_amount">&euro; 0</span></p>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="createAdvanceInvoice">
                    {{ KJLocalization::translate('Admin - Facturen', 'Voorschotfactuur maken', 'Voorschotfactuur maken')}}
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    {{ KJLocalization::translate('Algemeen', 'Sluiten', 'Sluiten')}}
                </button>
            </div>
        </div>
    </div>
</div>