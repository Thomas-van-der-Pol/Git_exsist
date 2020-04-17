{{ Form::open([
    'method' => 'post',
    'id' => 'detailFormTasksCustomMap',
    'class' => 'kt-form',
    'novalidate'
]) }}
    <div class="kt-portlet__body">
        {{ Form::hidden('ID', ( $item ? $item->ID : -1 )) }}
        {{ Form::hidden('FK_CORE_USER', ( $item ? $item->FK_CORE_USER : Auth::guard()->user()->ID )) }}

        <div class="row">
            <div class="col-xl-12 col-lg-6">
                {{ KJField::text('NAME', KJLocalization::translate('Admin - Taken', 'Naam map', 'Naam map'), $item ?  $item->NAME : '' , true, ['data-screen-mode' => 'read, edit']) }}
            </div>
        </div>
    </div>
{{ Form::close() }}