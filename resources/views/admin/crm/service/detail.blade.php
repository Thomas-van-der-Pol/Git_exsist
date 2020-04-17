{{ Form::open([
    'method' => 'post',
    'id' => 'detailFormProduct',
    'class' => 'kt-form',
    'novalidate'
]) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}
    {{ Form::hidden('requester_table', Auth::guard()->user()->getTable()) }}
    {{ Form::hidden('requester_item', Auth::guard()->user()->ID) }}

    <div class="row">
        <div class="col-xl-4 col-lg-6">
            {{ KJField::number('DIFFERENT_PRICE', KJLocalization::translate('Admin - CRM', 'Afwijkende prijs', 'Afwijkende prijs'), $item->PriceDecimal ?? '' , true, ['data-screen-mode' => 'read, edit', 'step' => '.01'],['right'=>['&euro;']]) }}

            @if($item->product->FK_DOCUMENT > 0)
                {{ KJField::link('DEFAULT_GUIDLINES', KJLocalization::translate('Admin - CRM', 'Standaard richtlijnen', 'Standaard richtlijnen'), $item->product->document->TITLE, 'javascript:;', true, ['data-screen-mode' => 'read, edit', 'class' => 'requestDocuments', 'data-id' => $item->product->document->ID]) }}
            @else
                {{ KJField::text('DEFAULT_GUIDLINES', KJLocalization::translate('Admin - CRM', 'Standaard richtlijnen', 'Standaard richtlijnen'), '-', true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
            @endif

            <div class="form-group mt-5">
                <div class="md-form">
                    <label class="active" style="top: -14px;">{{ KJLocalization::translate('Admin - CRM', 'Afwijkende richtlijnen', 'Afwijkende richtlijnen') }}</label>
                </div>
                <div class="kt-uppy singleFileUpload" id="uppy_uploader_{{ $item->ID }}" data-id="{{ $item->ID }}">
                    <div class="kt-uppy__wrapper" id="uppy_wrapper" @if($item->document)style="display:none;"@endif></div>
                    <div class="kt-uppy__list" id="uppy_list">
                        @if($item->document)
                            <div class="kt-uppy__list-item" data-id="{{ $item->document->ID }}">
                                <div class="kt-uppy__list-label">
                                    <a href="javascript:;" class="requestDocuments" data-id="{{ $item->document->ID }}">{{ $item->document->TITLE }}</a>
                                </div>
                                <span class="kt-uppy__list-remove" data-id="{{ $item->document->ID }}">
                                    <i class="flaticon2-cancel-music"></i>
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="kt-uppy__status" id="uppy_status"></div>
                    <div class="kt-uppy__informer kt-uppy__informer--min" id="uppy_informer"></div>
                </div>
                <span class="form-text text-muted">{{ KJLocalization::translate('Admin - Producten & diensten', 'PDF & Word bestanden', 'PDF & Word bestanden') }} : *.pdf *.docx (max 2MB.)</span>
            </div>
        </div>
    </div>

    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveDifferentPriceNew',
            'btnCancelDifferentPriceNew',
            true,
            [
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            ]
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveDifferentPrice',
            'btnCancelDifferentPrice',
            true,
            [
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan')
            ]
        ) }}
    @endif
</div>
{{ Form::close() }}