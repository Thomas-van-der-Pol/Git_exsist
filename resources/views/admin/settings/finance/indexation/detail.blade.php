{{ Form::open(array(
    'method' => 'post',
    'id' => 'detailFormIndexation',
    'class' => 'kt-from',
    'novalidate'
)) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}

    <div class="row">
        <div class="col-xl-4 col-lg-6">
            {{ KJField::text('DESCRIPTION', KJLocalization::translate('Admin - Financieel', 'Omschrijving', 'Omschrijving'), $item ? $item->DESCRIPTION : '') }}
            {{ KJField::number('PERCENTAGE', KJLocalization::translate('Admin - Financieel', 'Percentage', 'Percentage'), $item ? $item->numberFormatField($item->PERCENTAGE) : '', true, [], [
                'right' => [['type' => 'text', 'caption' => '%', 'class' => '']]]
            ) }}
            {{ KJField::checkbox('DONT_APPLY_INDEXATION', KJLocalization::translate('Admin - Financieel', 'Geen indexatie uitvoeren', 'Geen indexatie uitvoeren'), true, $item ? ($item->DONT_APPLY_INDEXATION) : false) }}
        </div>
    </div>

    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveIndexationNew',
            'btnCancelIndexationNew',
            true,
            array(
                'saveText'      => KJLocalization::translate('Instellingen - Indexatie', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText'    => KJLocalization::translate('Instellingen - Indexatie', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary',
                'removePadding', false
            )
        ) }}
    @else
        {{ KJField::saveCancel('btnSaveIndexation', 'btnCancelIndexation', true, ['removePadding', false]) }}
    @endif
</div>

{{ Form::close() }}