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
            {{ KJField::number('QUANTITY', KJLocalization::translate('Admin - Dossiers', 'Aantal', 'Aantal'), $item ? $item->QUANTITY : '') }}

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