<div class="kt-widget__top">
    <div class="kt-widget__content">
        <div class="kt-widget__head">
            <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/product') }}" class="back-button"></a>
            <h4>{{ ( $item ? $item->title : KJLocalization::translate('Admin - Interventies', 'Nieuwe interventie', 'Nieuwe interventie') ) }} <button type="button" class="btn btn-sm btn-icon setEditMode" data-target="default"><i class="fa fa-pen"></i></button></h4>

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
                            {{ KJField::text('DESCRIPTION_INT', KJLocalization::translate('Admin - Interventies', 'Omschrijving intern', 'Omschrijving intern'), ( $item ? $item->DESCRIPTION_INT : '' ), true, ['required', 'data-screen-mode' => 'read, edit']) }}
                            {{ KJField::text('DESCRIPTION_EXT', KJLocalization::translate('Admin - Interventies', 'Omschrijving extern', 'Omschrijving extern'), ( $item ? $item->DESCRIPTION_EXT : '' ), true, ['required', 'data-screen-mode' => 'read, edit']) }}
                        </div>

                        <div class="col-xl-4 col-lg-6">
                            {{ KJField::text('PRICE_READ',KJLocalization::translate('Admin - Interventies', 'Prijs exclusief', 'Prijs exclusief'), $item ? $item->getPriceFormattedAttribute() : '', true, ['data-screen-mode' => 'read']) }}
                            {{ KJField::number('PRICE',KJLocalization::translate('Admin - Interventies', 'Prijs exclusief', 'Prijs exclusief'), $item ? number_format((float)$item->PRICE,2, '.', '') : 0, true, ['required', 'data-screen-mode' => 'edit'], ['right' => [['type' => 'text', 'caption' => '&euro;']] ]) }}

                            {{ KJField::select('FK_FINANCE_LEDGER', KJLocalization::translate('Admin - Interventies', 'Grootboekrekening', 'Grootboekrekening'), $ledgers, ( $item ? $item->FK_FINANCE_LEDGER : '' ), true, 0, ['required', 'data-screen-mode' => 'read, edit']) }}
                            {{ KJField::select('FK_FINANCE_VAT', KJLocalization::translate('Admin - Interventies', 'Btw', 'Btw'), $vat, ( $item ? $item->FK_FINANCE_VAT : '' ), true, 0,['required', 'data-screen-mode' => 'read, edit']) }}
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