{{--<div class="kt-portlet__body">--}}
    {{--<div id="document_container">--}}
        {{--<div class="kt-form kt-form--label-right">--}}
            {{--<div class="row align-items-center">--}}
                {{--<div class="col order-2 order-xl-2">--}}
                    {{--<a href="javascript:;" data-id="{{ $item->ID }}" data-relation="{{ $item->FK_CRM_RELATION }}" class="btn btn-brand btn-sm btn-upper pull-right shareDocuments">--}}
                        {{--<i class="fa fa-paper-plane"></i>--}}
                        {{--{{ KJLocalization::translate('Admin - Project', 'Deel documenten', 'Deel documenten')}}--}}
                    {{--</a>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}

@include('core.document.main', [
    'FK_TABLE' => $item->getTable(),
    'FK_ITEM' => $item->ID,
    'options' => [
        'editable' => true,
        'uploader_table' => Auth::guard('admin')->user()->getTable(),
        'uploader_item' => Auth::guard('admin')->user()->ID
    ]
])