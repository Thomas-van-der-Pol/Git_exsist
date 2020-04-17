{{ Form::open(array(
    'method' => 'post',
    'id' => 'detailFormRole',
    'class' => 'kt-form',
    'novalidate'
)) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}

    <div class="row">
        <div class="col-xl-4 col-lg-6">
            {{ KJField::text('DESCRIPTION', KJLocalization::translate('Admin - Rollen & rechten', 'Omschrijving', 'Omschrijving'), $item ? $item->DESCRIPTION : '') }}
        </div>
    </div>

    <div class="row mt-2">
        <div class="col">
            <h5 class="mb-3">{{ KJLocalization::translate('Admin - Rollen & rechten', 'Gekoppelde rechten', 'Gekoppelde rechten') }}</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-4 col-lg-6">
            <div class="kt-checkbox-list">
                @foreach($permissions as $permission)
                    {{ KJField::checkbox('PERMISSIONS[]', $permission->DESCRIPTION, $permission->ID, ( in_array($permission->ID,$rolepermissions->toArray()) ) ? 1 : 0, FALSE ) }}
                @endforeach
            </div>
        </div>
    </div>

    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveRoleNew',
            'btnCancelRoleNew',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            )
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveRole',
            'btnCancelRole',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
            )
        ) }}
    @endif
</div>
{{ Form::close() }}