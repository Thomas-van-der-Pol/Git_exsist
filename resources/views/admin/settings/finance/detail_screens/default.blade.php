<div class="kt-widget__top">
    <div class="kt-widget__content">
        <div class="kt-widget__head">
            <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/finance') }}" class="back-button"></a>
            <h4>{{ ($item ? $item->title : KJLocalization::translate('Admin - Financieel', 'Nieuwe administratie', 'Nieuwe administratie')) }} <button type="button" class="btn btn-sm btn-icon setEditMode" data-target="default"><i class="fa fa-pen"></i></button></h4>

            @if($item)
                <div class="kt-widget__action">
                    @if($item->ACTIVE ?? true)
                        <button type="button" class="btn btn-danger btn-sm btn-upper activateItem" data-id="{{ $item->ID }}">{{ KJLocalization::translate('Algemeen', 'Archiveren', 'Archiveren') }}</button>
                    @else
                        <button type="button" class="btn btn-success btn-sm btn-upper activateItem" data-id="{{ $item->ID }}">{{ KJLocalization::translate('Algemeen', 'Activeren', 'Activeren') }}</button>
                    @endif
                </div>
            @endif
        </div>

        <div class="kt-widget__info mt-3">
            <div class="kt-widget__desc">
                {{ Form::open(array(
                    'method' => 'post',
                    'id' => 'label_default',
                    'class' => 'kt-form',
                    'novalidate'
                )) }}
                {{ Form::hidden('ID', $item ? $item->ID : -1) }}
                {{ Form::hidden('requester_table', Auth::guard()->user()->getTable()) }}
                {{ Form::hidden('requester_item', Auth::guard()->user()->ID) }}

                <div class="row">
                    <div class="col-xl-4 col-lg-6">
                        {{ KJField::text('DESCRIPTION', KJLocalization::translate('Admin - Financieel', 'Omschrijving', 'Omschrijving'), $item ? $item->DESCRIPTION : '', true, ['required', 'data-screen-mode' => 'edit']) }}

                        {{ KJField::text('EMAIL_SENDER_NAME', KJLocalization::translate('Admin - Financieel', 'Afzender naam', 'Afzender naam'), $item ? $item->EMAIL_SENDER_NAME : '', true, ['required', 'data-screen-mode' => 'read, edit']) }}
                        {{ KJField::email('EMAIL_SENDER_EMAIL', KJLocalization::translate('Admin - Financieel', 'Afzender e-mail', 'Afzender e-mail'), $item ? $item->EMAIL_SENDER_EMAIL : '', ['data-screen-mode' => 'read, edit']) }}

                        {{ KJField::text('IBAN_NUMBER', KJLocalization::translate('Admin - Financieel', 'IBAN', 'IBAN'), $item ? $item->IBAN_NUMBER : '', true, ['data-screen-mode' => 'read, edit']) }}
                        {{ KJField::text('BIC_NUMBER', KJLocalization::translate('Admin - Financieel', 'BIC', 'BIC'), $item ? $item->BIC_NUMBER : '', true, ['data-screen-mode' => 'read, edit']) }}
                        {{ KJField::text('VAT_NUMBER', KJLocalization::translate('Admin - Financieel', 'Btw nummer', 'Btw nummer'), $item ? $item->VAT_NUMBER : '', true, ['data-screen-mode' => 'read, edit']) }}
                    </div>

                    <div class="col-xl-4 col-lg-6">
                        <div class="md-form mt-3 mb-5">
                            <div class="form-group">
                                <div class="kt-avatar" id="LOGO_EMAIL_SELECT">
                                    <div class="kt-avatar__holder" style="width: 350px; background-image: url({{ asset($item ? ((($item->LOGO_EMAIL ?? '') != '') ? config('app.cdn_url') . $item->LOGO_EMAIL : '/assets/theme/img/missing_logo_thumbnail.jpg') : '/assets/theme/img/missing_logo_thumbnail.jpg') }})"></div>
                                    <label class="kt-avatar__upload default-label" data-toggle="kt-tooltip" title="" data-original-title="{{ KJLocalization::translate('Algemeen', 'Afbeelding veranderen', 'Afbeelding veranderen') }}">
                                        <i class="fa fa-pen"></i>
                                        <input type="file" name="LOGO_EMAIL">
                                    </label>
                                    <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="{{ KJLocalization::translate('Algemeen', 'Afbeelding herstellen', 'Afbeelding herstellen') }}">
                                        <i class="fa fa-times"></i>
                                    </span>
                                </div>
                                <span class="form-text text-muted">{{ KJLocalization::translate('Algemeen', 'Afbeelding bestanden', 'Afbeelding bestanden') }}: *.jpg, *.jpeg, *.png</span>
                                <label class="active" style="top: -14px;">{{ KJLocalization::translate('Admin - Financieel', 'Logo e-mail', 'Logo e-mail') }}</label>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <div class="md-form">
                                <label class="active" style="top: -14px;">{{ KJLocalization::translate('Admin - Financieel', 'Briefpapier', 'Briefpapier') }}</label>
                            </div>
                            <div class="kt-uppy singleFileUpload" id="uppy_uploader" data-id="{{ $item->ID }}">
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
                            <span class="form-text text-muted">{{ KJLocalization::translate('Algemeen', 'PDF bestanden', 'PDF bestanden') }} : *.pdf (max 2MB.)</span>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-lg-12">
                        @if(!$item)
                            {{ KJField::saveCancel(
                                'btnSaveLabelNew',
                                'btnCancelLabelNew',
                                true,
                                array(
                                    'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                                    'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
                                )
                            ) }}
                        @else
                            {{ KJField::saveCancel(
                                'btnSaveLabel',
                                'btnCancelLabel',
                                true,
                                array(
                                    'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                                    'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
                                )
                            ) }}
                        @endif
                    </div>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>