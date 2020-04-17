<div class="kt-widget__top">
    <div class="kt-widget__content">
        <div class="kt-widget__head">
            <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/workflow') }}" class="back-button"></a>
            <h4>{{ ($item ? $item->title : KJLocalization::translate('Admin - Workflows', 'Nieuwe workflow', 'Nieuwe workflow')) }} <button type="button" class="btn btn-sm btn-icon setEditMode" data-target="default"><i class="fa fa-pen"></i></button></h4>

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
                    'id' => 'workflow_default',
                    'class' => 'kt-form',
                    'novalidate'
                )) }}
                {{ Form::hidden('ID', $item ? $item->ID : -1) }}

                <div class="row">
                    <div class="col-xl-4 col-lg-6">
                        {{ KJField::text('DESCRIPTION', KJLocalization::translate('Admin - Workflows', 'Omschrijving', 'Omschrijving'), $item ? $item->DESCRIPTION : '', true, ['required', 'data-screen-mode' => 'edit']) }}
                        {{-- Required --}} {{ KJField::select('FK_CORE_DROPDOWNVALUE', KJLocalization::translate('Admin - Workflows', 'Projecttype', 'Projecttype'), $project_types, $item ? $item->FK_CORE_DROPDOWNVALUE : '', true, config('dropdown_type.TYPE_PROJECTTYPE'), ['required', 'data-screen-mode' => 'read, edit']) }}
                        {{ KJField::select('FK_CORE_WORKFLOWSTATE_INVOICE', KJLocalization::translate('Admin - Workflows', 'Factureren bij status', 'Factureren bij status'), $workflow_states, $item ? $item->FK_CORE_WORKFLOWSTATE_INVOICE : '', true, 0, ['data-screen-mode' => 'read, edit']) }}
                    </div>

                    <div class="col-xl-4 col-lg-6">

                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        @if(!$item)
                            {{ KJField::saveCancel(
                                'btnSaveWorkflowNew',
                                'btnCancelWorkflowNew',
                                true,
                                array(
                                    'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                                    'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
                                )
                            ) }}
                        @else
                            {{ KJField::saveCancel(
                                'btnSaveWorkflow',
                                'btnCancelWorkflow',
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