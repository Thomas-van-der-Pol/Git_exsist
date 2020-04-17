<div class="kt-widget__top">
    <div class="kt-widget__content">
        <div class="kt-widget__head">
            <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/tasklist') }}" class="back-button"></a>
            <h4>{{ ($item ? $item->NAME : KJLocalization::translate('Admin - Tasklists', 'Nieuwe takenlijst', 'Nieuwe takenlijst')) }} <button type="button" class="btn btn-sm btn-icon setEditMode" data-target="default"><i class="fa fa-pen"></i></button></h4>

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
                    'id' => 'tasklist_default',
                    'class' => 'kt-form',
                )) }}
                {{ Form::hidden('ID', $item ? $item->ID : -1) }}

                <div class="row">
                    <div class="col-xl-4 col-lg-6">
                        {{-- Required --}} {{ KJField::text('NAME', KJLocalization::translate('Algemeen', 'Naam', 'Naam'), $item ? $item->NAME : '', true, ['required', 'data-screen-mode' => 'read,edit']) }}

                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        @if(!$item)
                            {{ KJField::saveCancel(
                                'btnSaveTasklistNew',
                                'btnCancelTasklistNew',
                                true,
                                array(
                                    'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                                    'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
                                )
                            ) }}
                        @else
                            {{ KJField::saveCancel(
                                'btnSaveTasklist',
                                'btnCancelTasklist',
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