{{ Form::open([
    'method' => 'post',
    'id' => 'detailFormProduct',
    'class' => 'kt-form',
    'novalidate'
]) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}

    <div class="row">
        <div class="col-xl-4 col-lg-6">
            {{ KJField::text('DESCRIPTION_EXT', KJLocalization::translate('Admin - Dossiers', 'Omschrijving extern', 'Omschrijving extern'), $item ? $item->DESCRIPTION_EXT : '') }}
{{--            {{ KJField::number('QUANTITY', KJLocalization::translate('Admin - Dossiers', 'Aantal', 'Aantal'), $item ? $item->QUANTITY : '') }}--}}

            @php
                $relationButtons = [];
                $relationButtons[] = ['type' => 'button', 'caption' => KJLocalization::translate('Admin - Dossiers', 'Relatie', 'Relatie'), 'class' => 'btn btn-primary btn-sm selectRelation'];
                if (Auth::guard()->user()->hasPermission(config('permission.CRM'))) {
                    $relationButtons[] = ['type' => 'button', 'caption' => KJLocalization::translate('Algemeen', 'Openen', 'Openen'), 'class' => 'btn btn-dark btn-sm openRelation'];
                }
            @endphp

            {{--Required--}} {{ KJField::text('PROVIDER_NAME', KJLocalization::translate('Admin - Dossiers', 'Provider', 'Provider'), $item ? ($item->relation ? $item->relation->title : '-') : '-', true, ['readonly'], [
                 'right' => $relationButtons
            ]) }}
            {{ Form::hidden('FK_CRM_RELATION', $item ? $item->FK_CRM_RELATION : null) }}

            {{ KJField::number('PRICE', KJLocalization::translate('Admin - Dossiers', 'Stukprijs exclusief', 'Stukprijs exclusief'), $item ? number_format((float)$item->PRICE,2, '.', '') : 0, true, [], ['right' => [['type' => 'text', 'caption' => '&euro;']] ]) }}
            {{ KJField::text('QUOTATION_NUMBER', KJLocalization::translate('Admin - Facturen', 'Offertenummer', 'Offertenummer'), $item ? $item->QUOTATION_NUMBER : '', true, []) }}

            @if($compensation_can_change)
                {{ KJField::checkbox('COMPENSATED', KJLocalization::translate('Admin - Dossiers', 'Wordt vergoed', 'Wordt vergoed'), true, ( $item ? $item->COMPENSATED : false ), true, ['data-screen-mode' => 'read,edit', 'data-id' => $item ? $item->project->ID : -1]) }}
            @else
                {{ KJField::text('COMPENSATED', KJLocalization::translate('Admin - Taken', 'Wordt vergoed', 'Wordt vergoed'), $item ? ($item->COMPENSATED ? KJLocalization::translate('Admin - Dossiers', 'Ja', 'Ja') : KJLocalization::translate('Admin - Dossiers', 'Nee', 'Nee')) : '' , true, [$compensation_readonly, 'data-screen-mode' => 'read, edit']) }}
            @endif

            {{ Form::hidden('PROJECT_START_DATE', $item ? ($item->project ? $item->project->START_DATE : '') : '') }}
            {{ Form::hidden('PROJECT_POLICY_NUMBER', $item ? ($item->project ? $item->project->POLICY_NUMBER : '') : '') }}
            {{ KJField::number('COMPENSATION_PERCENTAGE', KJLocalization::translate('Admin - Dossiers', 'Vergoedingspercentage', 'Vergoedingspercentage'), $item ? $item->getCompensationPercentageDecimalAttribute() : '', true, [$item ? ($item->COMPENSATED ? 'required' : 'disabled') : '',$compensation_readonly, 'data-screen-mode' => 'read, edit', 'min' => 0, 'max'=> 100], [
                'right' => [['type' => 'text', 'caption' => '%']]]
            ) }}
        </div>
    </div>

    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveQuantityNew',
            'btnCancelQuantityNew',
            true,
            [
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            ]
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveQuantity',
            'btnCancelQuantity',
            true,
            [
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan')
            ]
        ) }}
    @endif
</div>
{{ Form::close() }}