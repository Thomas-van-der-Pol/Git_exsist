var logo_user_select;

$(document).ready(function() {

    kjlocalization.create('Admin - Dossiers', [
        {'Selecteer dienst': 'Selecteer dienst'}
    ]);

    kjlocalization.create('Admin - Werknemers', [
        {'Emailadres leeg': 'E-mailadres is leeg. Wachtwoord kan niet worden gereset!'},
    ]);

    // Load screen
    loadScreen($('#default'), {
        url: '/admin/settings/user/detailScreen',
        mode: 'read',
        afterLoad: afterLoadScreen
    });

    // Load active sub screen
    $('.kt-widget__item--active').each(function() {
        loadScreen($(this), {
            url: '/admin/settings/user/detailScreen',
            afterLoad: afterLoadScreen
        });
    });

    // Switch tab action
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        loadScreen($(e.target), {
            url: '/admin/settings/user/detailScreen',
            afterLoad: afterLoadScreen
        });
    });

    // Edit action
    $('body').on('click', '.setEditMode', function(e) {
        e.preventDefault();

        var target = $(this).data('target');
        setScreenMode($('#'+target), 'edit');
    });

    // Cancel action
    $('body').on('click', '#btnCancelUser, #btnCancelUserPermission', function(e) {
        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        setScreenMode(container, 'read');
    });

    // Save action
    $('body').on('click', '#btnSaveUser, #btnSaveUserNew, #btnSaveUserPermission', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        save($(this), '/admin/settings/user', null, false, null, function(data) {
            if (data.success === true) {
                // When inserted then reload
                if (data.new == true) {
                    window.location = '/admin/settings/user/detail/' + data.id;
                }

                loadScreen(container, {
                    url: '/admin/settings/user/detailScreen',
                    mode: 'read',
                    afterLoad: afterLoadScreen
                });
            }
        });
    });

    $('body').on('click', '#btnCancelUserNew', function(e) {
        e.preventDefault();

        window.location = '/admin/settings/user';
    });

    $('body').on('click', '.activateItem', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/settings/user/' + id, null, false,
            function(result) {
                if (result.success) {
                    loadScreen($('#default'), {
                        url: '/admin/settings/user/detailScreen',
                        mode: 'read',
                        afterLoad: afterLoadScreen
                    });
                }
            }
        );
    });

    $('body').on('click', '.anonymizeUser', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        var formData = new FormData();

        formData.append('id', id);

        swal.fire({
            title: kjlocalization.get('algemeen', 'anonimiseren_titel'),
            text: kjlocalization.get('algemeen', 'anonimiseren_text'),
            type: 'warning',
            width: 600,
            showCancelButton: true,
            confirmButtonText: kjlocalization.get('algemeen', 'doorgaan'),
            cancelButtonText: kjlocalization.get('algemeen', 'annuleren')
        }).then(function(result) {
            if (result.value) {
                kjrequest('POST', '/admin/settings/user/anonimyze', formData, true, function (result) {
                    if (result.success) {
                        $.notify({message: result.message}, {type: 'success'});

                        loadScreen($('#default'), {
                            url: '/admin/settings/user/detailScreen',
                            mode: 'read',
                            afterLoad: afterLoadScreen
                        });
                    }
                });

            } else if (result.dismiss === 'cancel') {
                // result.dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
            }
        });
    });

    $('body').on('click', '.resetPassword', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        var email = $(this).data('email');
        var btn = $(this);

        if (email === '') {
            swal({
                text: kjlocalization.get('admin_-_werknemers', 'emailadres_leeg'),
                type: 'error'
            });

            return false;
        }

        var formData = new FormData();
        formData.append('id', id);
        formData.append('email', email);

        KTApp.progress(btn);

        kjrequest('POST', '/admin/settings/user/resetPassword', formData, false,
            function(result) {
                if (result.success) {
                    KTApp.unprogress(btn);
                }
            }
        );
    });

    $('body').on('change', '#RECEIVE_NOTIFICATION', function(e) {
        if($(this).is(':checked')) {
            $('.RECEIVE_NOTIFICATION_SETTING').removeClass('kt-hide');

            $('#FK_CORE_ROLE_NOTIFICATION').attr('required', true);
        } else {
            $('.RECEIVE_NOTIFICATION_SETTING').addClass('kt-hide');

            $('#FK_CORE_ROLE_NOTIFICATION').removeAttr('required');
        }
    });

    $('body').on('change', '#DUMMY_NOTIFICATION', function(e) {
        if($(this).is(':checked')) {
            $('.SHOW_NOTIFICATION').removeClass('kt-hide');

            $('#DUMMY_FK_CORE_ROLE_NOTIFICATION').attr('required', true);
            $('#DUMMY_DATE_NOTIFICATION').attr('required', true);
        } else {
            $('.SHOW_NOTIFICATION').addClass('kt-hide');

            $('#DUMMY_FK_CORE_ROLE_NOTIFICATION').removeAttr('required');
            $('#DUMMY_DATE_NOTIFICATION').removeAttr('required');
        }
    });
});

function afterLoadScreen(id, screen, data) {
    if (screen === 'default') {
        logo_user_select = new KTAvatar('LOGO_USER_SELECT');
        $('#RECEIVE_NOTIFICATION').trigger('change');
    }
}