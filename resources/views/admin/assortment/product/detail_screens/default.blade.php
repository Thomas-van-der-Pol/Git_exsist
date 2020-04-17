<div class="kt-widget__top">
    <div class="kt-widget__content">
        <div class="kt-widget__head">
            <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/product') }}" class="back-button"></a>
            <h4>{{ ( $item ? $item->title : KJLocalization::translate('Admin - Producten & diensten', 'Nieuw product of dienst', 'Nieuw product of dienst') ) }} <button type="button" class="btn btn-sm btn-icon setEditMode" data-target="default"><i class="fa fa-pen"></i></button></h4>

            <div class="kt-widget__action">
                @if($item)
                    @if($item->ACTIVE ?? true)
                        <button type="button" class="btn btn-danger btn-sm btn-upper activateItem" data-id="{{ ( $item ? $item->ID : 0 ) }}">{{ KJLocalization::translate('Algemeen', 'Archiveren', 'Archiveren') }}</button>
                    @else
                        <button type="button" class="btn btn-success btn-sm btn-upper activateItem" data-id="{{ ( $item ? $item->ID : 0 )}}">{{ KJLocalization::translate('Algemeen', 'Activeren', 'Activeren') }}</button>
                    @endif
                @endif
            </div>
        </div>

        <div class="kt-widget__info mt-3">
            <div class="kt-widget__desc">
                {{ Form::open(array(
                    'method' => 'post',
                    'id' => 'detailFormProduct',
                    'class' => 'kt-form',
                    'novalidate'
                )) }}
                {{ Form::hidden('ID', ( $item ? $item->ID : -1 )) }}
                {{ Form::hidden('requester_table', Auth::guard()->user()->getTable()) }}
                {{ Form::hidden('requester_item', Auth::guard()->user()->ID) }}

                    <div class="row">
                        <div class="col-xl-4 col-lg-6">
                            {{ KJField::select('FK_ASSORTMENT_PRODUCT_TYPE', KJLocalization::translate('Admin - Producten & diensten', 'Producttype', 'Producttype'), $producttypes, ( $item ? $item->FK_ASSORTMENT_PRODUCT_TYPE : '' ), true, 0,['required', 'data-screen-mode' => 'read, edit']) }}
                            {{ KJField::text('DESCRIPTION_INT', KJLocalization::translate('Admin - Producten & diensten', 'Omschrijving intern', 'Omschrijving intern'), ( $item ? $item->DESCRIPTION_INT : '' ), true, ['required', 'data-screen-mode' => 'read, edit']) }}
                            {{ KJField::text('DESCRIPTION_EXT', KJLocalization::translate('Admin - Producten & diensten', 'Omschrijving extern', 'Omschrijving extern'), ( $item ? $item->DESCRIPTION_EXT : '' ), true, ['required', 'data-screen-mode' => 'read, edit']) }}
                        </div>

                        <div class="col-xl-4 col-lg-6">
                            {{ KJField::text('PRICE_READ',KJLocalization::translate('Admin - Producten & diensten', 'Prijs exclusief', 'Prijs exclusief') . (( $item && ($item->FK_ASSORTMENT_PRODUCT_TYPE == config('product_type.TYPE_SERVICE')) ) ? ' '.KJLocalization::translate('Algemeen', 'per uur', ' per uur') : ''), $item ? $item->getPriceFormattedAttribute() : '', true, ['data-screen-mode' => 'read']) }}
                            {{ KJField::number('PRICE',KJLocalization::translate('Admin - Producten & diensten', 'Prijs exclusief', 'Prijs exclusief') . (( $item && ($item->FK_ASSORTMENT_PRODUCT_TYPE == config('product_type.TYPE_SERVICE')) ) ? ' '.KJLocalization::translate('Algemeen', 'per uur', ' per uur') : ''), $item ? number_format((float)$item->PRICE,2, '.', '') : 0, true, ['required', 'data-screen-mode' => 'edit'], ['right' => [['type' => 'text', 'caption' => '&euro;']] ]) }}

                            {{ KJField::select('FK_FINANCE_LEDGER', KJLocalization::translate('Admin - Producten & diensten', 'Grootboekrekening', 'Grootboekrekening'), $ledgers, ( $item ? $item->FK_FINANCE_LEDGER : '' ), true, 0, ['required', 'data-screen-mode' => 'read, edit']) }}
                            {{ KJField::select('FK_FINANCE_VAT', KJLocalization::translate('Admin - Producten & diensten', 'Btw', 'Btw'), $vat, ( $item ? $item->FK_FINANCE_VAT : '' ), true, 0,['required', 'data-screen-mode' => 'read, edit']) }}
                            {{ KJField::select('FK_FINANCE_INDEXATION', KJLocalization::translate('Admin - Producten & diensten', 'Jaarlijkse indexatie', 'Jaarlijkse indexatie'), $indexations, ( $item ? $item->FK_FINANCE_INDEXATION : '' ), true, 0, ['required', 'data-screen-mode' => 'read, edit']) }}
                        </div>

                        <div class="col-xl-4 col-lg-6">
                            <div class="assortment_producttype_service {{ ( $item && ($item->FK_ASSORTMENT_PRODUCT_TYPE == config('product_type.TYPE_SERVICE')) ) ? '' : 'kt-hide' }}" data-id="{{ config('product_type.TYPE_SERVICE') }}">
                                @if($item)
                                    <div class="form-group mt-2">
                                        <div class="md-form">
                                            <label class="active" style="top: -14px;">{{ KJLocalization::translate('Admin - Producten & diensten', 'Richtlijnen', 'Richtlijnen') }}</label>
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
                                        <span class="form-text text-muted">{{ KJLocalization::translate('Admin - Producten & diensten', 'PDF & Word bestanden', 'PDF & Word bestanden') }} : *.pdf *.docx (max 2MB.)</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(!$item)
                        {{ KJField::saveCancel(
                            'btnSaveProductNew',
                            'btnCancelProductNew',
                            true,
                            array(
                                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
                            )
                        ) }}
                    @else
                        {{ KJField::saveCancel(
                            'btnSaveProduct',
                            'btnCancelProduct',
                            true,
                            array(
                                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
                            )
                        ) }}
                    @endif
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>