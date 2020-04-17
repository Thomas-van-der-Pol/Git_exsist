{{ Form::open([
    'method' => 'post',
    'id' => 'detailFormFunctionModal',
    'class' => 'kt-form',
    'novalidate'
]) }}
    <div class="kt-portlet__body">
        <div class="row">
            <div class="col-xl-12 col-lg-6">
                @if($type == 'shiftDeadline')
                    {{ KJField::number('SHIFT_DATES',KJLocalization::translate('Admin - Taken', 'Aantal dagen verschuiven', 'Aantal dagen verschuiven'), 0, true, ['required', 'min' => 0, 'steps' => 1]) }}
                @else
                    {{ KJField::select('FK_CORE_USER_ASSIGNEE', KJLocalization::translate('Admin - Taken', 'Toegewezen aan', 'Toegewezen aan'), $users, '', true, 0, ['required']) }}
                @endif
            </div>
        </div>
    </div>
{{ Form::close() }}