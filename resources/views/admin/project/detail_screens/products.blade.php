<div class="kt-portlet__body">
    <div class="kt-form kt-form--label-right">
        <div class="row align-items-center">
            <div class="col-auto order-2 order-xl-1">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="form-inline md-form filter-icon">
                            {{ Form::text(
                                'ADM_PROJECT_PRODUCT_FILTER_SEARCH',
                                '',
                                array(
                                    'class'         => 'form-control filter',
                                    'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken').'...',
                                    'id'            => 'ADM_PROJECT_PRODUCT_FILTER_SEARCH',
                                )
                            ) }}
                        </div>
                    </div>
                </div>
            </div>

            @if(($item->INVOICING_COMPLETE ?? false) == false)
                <div class="col order-2 order-xl-2">
                    <a href="javascript:;" id="addProducts" class="btn btn-success btn-sm btn-upper pull-right" data-id="{{ $item->ID }}" data-type="{{ config('product_type.TYPE_PRODUCT') }}">
                        <i class="fa fa-plus-square"></i>
                        {{ KJLocalization::translate('Admin - CRM', 'Producten', 'Producten')}}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="kt-separator m-0"></div>

@php
    $customEditButton = [];

    if (($item->INVOICING_COMPLETE ?? false) == false) {
        $customEditButton = array(
            'end' => [
                [
                    'HTML'  => '<a href="javascript:;" data-id="{idField}" data-type="'.config('product_type.TYPE_PRODUCT').'" class="btn btn-bold btn-label-danger btn-sm btn-icon deleteProduct" title="' . KJLocalization::translate("Algemeen", "Verwijderen", "Verwijderen") . '" ><i class="la la-close""></i></a>'
                ]
            ]
        );
    }
@endphp

{{ KJDatatable::create(
    'ADM_PROJECT_PRODUCTS_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/project/product/allByProjectProductDatatable/' . $item->ID,
        'editable' => (($item->INVOICING_COMPLETE ?? false) == false),
        'editURL' => '/admin/project/product/detailRendered/',
        'saveURL' => '/admin/project/product/save',
        'searchinput' => '#ADM_PROJECT_PRODUCT_FILTER_SEARCH',
        'parentid' => $item->ID,
        'pagination' => false,
        'pagesize' => 99999,
        'columns' => array(
            array(
                'field' => 'QUANTITY',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Aantal', 'Aantal'),
                'width' => 50
            ),
            array(
                'field' => 'FULL_PRODUCT',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Omschrijving intern', 'Omschrijving intern')
            ),
            array(
                'field' => 'RELATION',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Provider', 'Provider')
            ),
            array(
                'field' => 'PRICE',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Stukprijs', 'Stukprijs')
            ),
            array(
                'field' => 'PRICE_TOTAL',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Subtotaal', 'Subtotaal')
            )
        ),
        'customEditButtons' => $customEditButton
    )
) }}

<div class="kt-portlet__body kt-portlet__body--fit-y pb-4">
    <div class="row m-0">
        <div class="col"></div>

        <div class="col-auto">
            <div class="row m-0">
                <div class="col"><h5 class="kt-font-bold">{{ KJLocalization::translate('Admin - Dossiers', 'Totaal', 'Totaal') }}</h5></div>
                <div style="width: 100px" class="text-right">
                    <h5 class="kt-font-bold" id="productTotal">{{--RENDERED--}}</h5>
                </div>
            </div>
        </div>
    </div>
</div>