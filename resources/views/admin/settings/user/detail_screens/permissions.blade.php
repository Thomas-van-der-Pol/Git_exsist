<div class="kt-portlet__body">
    {{ Form::open(array(
        'method' => 'post',
        'id' => 'user_permissions',
        'class' => 'kt-form',
        'novalidate'
    )) }}
        {{ Form::hidden('ID', $item ? $item->ID : -1) }}

        <div class="row">
            <div class="col">
                <h5 class="mb-3">{{ KJLocalization::translate('Admin - Werknemers', 'Algemeen', 'Algemeen') }} <button type="button" class="btn btn-sm btn-icon setEditMode" data-target="permissions"><i class="fa fa-pen"></i></button></h5>
            </div>
        </div>

        {{ KJField::checkbox('LOGIN_ENABLED', KJLocalization::translate('Admin - Werknemers', 'Mag inloggen', 'Mag inloggen'), true, $item ? $item->LOGIN_ENABLED : true, true, ['data-screen-mode' => 'read, edit']) }}

        <div class="row mt-4">
            <div class="col">
                <h5 class="mb-3">{{ KJLocalization::translate('Admin - Werknemers', 'Gekoppelde rollen', 'Gekoppelde rollen') }}</h5>
            </div>
        </div>

        {{-- ROLLEN WEERGAVE --}}
        <div class="md-form">
            <p data-screen-mode="read">
                @php($existingRoles = $item->roles->sortBy('DESCRIPTION'))
                @foreach($existingRoles as $role)
                    - {{ $role->DESCRIPTION }}<br/>
                @endforeach
            </p>
        </div>

        {{-- ROLLEN BEWERKEN --}}
        <div class="kt-checkbox-list">
            @foreach($roles as $role)
                {{ KJField::checkbox('ROLES[]', $role->DESCRIPTION, $role->ID, (in_array($role->ID, $userroles->toArray())) ? true : false, true, ['data-screen-mode' => 'edit']) }}
            @endforeach
        </div>

        {{ KJField::saveCancel(
            'btnSaveUserPermission',
            'btnCancelUserPermission',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
            )
        ) }}
    {{ Form::close() }}
</div>