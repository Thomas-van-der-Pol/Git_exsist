{{ Form::open([
    'method' => 'post',
    'id' => 'host_default',
    'class' => 'kt-form',
    'novalidate'
]) }}
    <div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
        {{ Form::hidden('ID', $item ? $item->ID : -1) }}

        <div class="row">
            <div class="col-xl-4 col-lg-6">
                {{--Required--}} {{ KJField::text('HOSTNAME', KJLocalization::translate('Admin - Werkstations', 'Werkstation', 'Werkstation'), $item ? $item->HOSTNAME : '', true, ['required']) }}
                {{--Required--}} {{ KJField::text('MAC_ADDRESS', KJLocalization::translate('Admin - Werkstations', 'MAC', 'MAC-adres'), $item ? $item->MAC_ADDRESS : '', true, ['required']) }}

                {{ Form::hidden('PRINTER_DEFAULT_DUMMY', $item ? $item->PRINTER_DEFAULT : '') }}
                {{--Required--}} {{ KJField::select('PRINTER_DEFAULT', KJLocalization::translate('Admin - Werkstations', 'Standaard printer', 'Standaard printer'), [], $item ? $item->PRINTER_DEFAULT : '', true, 0, ['required']) }}

                {{ Form::hidden('PRINTER_INVOICE_DUMMY', $item ? $item->PRINTER_INVOICE : '') }}
                {{ KJField::select('PRINTER_INVOICE', KJLocalization::translate('Admin - Werkstations', 'Factuur printer', 'Factuur printer'), [], $item ? $item->PRINTER_INVOICE : '') }}

            </div>
        </div>

        @if(!$item)
            {{ KJField::saveCancel(
                'btnSaveHostNew',
                'btnCancelHostNew',
                true,
                array(
                    'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                    'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
                )
            ) }}
        @else
            {{ KJField::saveCancel(
                'btnSaveHost',
                'btnCancelHost',
                true,
                array(
                    'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                    'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
                )
            ) }}
        @endif
    </div>
{{ Form::close() }}