$(document).ready(function() {
    // Alle localstorage clearen als cacheversion afwijkt
    var currentCacheVersion = $('meta[name="kj_cacheversion"]').attr('content');
    if (currentCacheVersion != undefined) {
        if (currentCacheVersion != window.localStorage.getItem('kj_cacheversion')) {
            window.localStorage.clear();
            console.log('LocalStorage gewist i.v.m. cache versie');
            window.localStorage.setItem('kj_cacheversion', currentCacheVersion);
            console.log('LocalStorage: cache versie gezet: ' + currentCacheVersion );
        }
    }

    // Focus zetten op inputveld wanneer op icon wordt geklikt
    $('.kt-input-icon span').on('click', function(e) {
        $(this).parent('.kt-input-icon').find('input').focus();
    });

    // Save value in session
    $('.hasSessionState').on('change', function(e) {
        if (($(this).data('module') !== undefined) && ($(this).data('key') !== undefined)) {
            if ($(this).hasClass('kjdaterangepicker-picker')) {
                var date = $(this).val().split(' / ');
                var startDate = date[0] ? date[0] : 'KJ-value-is-undefined-dus-verwijderen';
                var endDate = date[1] ? date[1] : 'KJ-value-is-undefined-dus-verwijderen';

                storeSession(
                    $(this).data('module'),
                    $(this).data('key') + '_startDate',
                    startDate
                );

                storeSession(
                    $(this).data('module'),
                    $(this).data('key') + '_endDate',
                    endDate
                );
            } else if ($(this).is(':checkbox')) {
                storeSession(
                    $(this).data('module'),
                    $(this).data('key'),
                    $(this).is(':checked') ? 1 : 0
                );
            } else {
                storeSession(
                    $(this).data('module'),
                    $(this).data('key'),
                    $(this).val() ? $(this).val() : 'KJ-value-is-undefined-dus-verwijderen'
                );
            }
        }
    });
});

function storeSession(module, key, value) {
    formdata = new FormData();
    formdata.append('module', module);
    formdata.append('key', key);
    formdata.append('value', value);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: window.storeSessionUrl,
        type: 'POST',
        contentType: false,
        processData: false,
        async: false,
        data: formdata
    });
}

function kjrequest(requestType, requestUrl, requestData, silentOnSuccess, callback, errorCallback) {
    var errorMessage = kjlocalization.get('algemeen', 'foutmelding');
    var successMessage = kjlocalization.get('algemeen', 'succesvol');

    var requestTypes = ['POST', "GET"];
    if (!(requestType.indexOf(requestType.toUpperCase()) > -1)) {
        $.notify({message:errorMessage + ' ' + 'ongeldige request type'},{type: 'danger', z_index: 99999});
        return false;
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: requestUrl,
        type: requestType.toUpperCase(),
        data: (requestType.toUpperCase() == 'POST' ? requestData : null),
        contentType: false,
        processData: false,
        success: function(data) {
            if (data.success) {
                if (!silentOnSuccess) {
                    $.notify({message: successMessage}, {type: 'success', z_index: 99999});
                }

                if (callback != null) {
                    callback(data);
                }

            } else {
                if(data.message) {
                    $.notify({message:errorMessage + ' ' + data.message},{type: 'danger', z_index: 99999});
                } else {
                    $.notify({message:errorMessage + ' unknown'},{type: 'danger', z_index: 99999});
                }

                if (errorCallback != null) {
                    errorCallback(data);
                }
            }
        },
        error: function(data) {
			if (data.status == 419) {
                window.location.reload();
            }
            $.notify({message:errorMessage + ' ' + data.responseJSON.message},{type: 'danger', z_index: 99999});

            if (errorCallback != null) {
                errorCallback(data);
            }
        }
    });
}

/*
 MATERIAL THEME
 */
function loadScreen(object, options = {})
{
    // Clear inactive tab-panes
    $('.tab-pane:not(.active)').empty();

    // Get options
    var url = options.url || 'unknown';
    var mode = options.mode || 'read';
    var afterLoad = options.afterLoad || null;
    var postData = options.postData || [];

    // Handle active tab
    var nav = object.closest('.nav');
    if (nav.length) {
        object.closest('.nav').find('a[data-toggle="tab"]').removeClass('kt-widget__item--active');
        object.addClass('kt-widget__item--active');
    }

    // Get variables
    var id = object.data('id');
    var screen = '';
    if (object.is('a')) {
        screen = object.attr('href').replace('#', '');
    } else {
        screen = object.attr('id');
    }

    // Reset mode when inserting
    if ((id === -1) && (mode === 'read')) {
        mode = 'edit';
    }

    var screenEl = $('#'+screen);
    var container = screenEl.closest('.kt-portlet');
    var detailWasOpen = screenEl.find('.kj_show_less').is(':visible');

    KTApp.block(container);

    var formData = new FormData();
    formData.append('ID', id);
    formData.append('SCREEN', screen);

    // Add optional post data
    postData.forEach(function(pair, index) {
        $.each(pair, function(key, value) {
            formData.append(key, value);
        });
    });

    // Load data
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,

        success: function(data) {
            if (data.success === true) {
                screenEl.html(data.view);
                if (detailWasOpen === true) {
                    screenEl.find('.kj_extra_details').toggle();
                    screenEl.find('.kj_show_more').toggle();
                    screenEl.find('.kj_show_less').toggle();
                }

                // Load fields
                loadDropdowns();
                loadDatePickers();
                loadDateTimePickers();
                loadKJPostcodeLookups();

                setScreenMode(screenEl, mode);

                if (afterLoad != null) {
                    afterLoad(id, screen, data);
                }

                KTApp.unblock(container);
            }
        }
    });
}

function setScreenMode(object, mode) {
    var disabled = false;

    var inputs = object.find('input:not(.filter):not(.exclude-screen-mode), textarea:not(.filter):not(.exclude-screen-mode), div.bootstrap-select:not(.filter):not(.exclude-screen-mode) button[data-toggle="dropdown"], p, a');

    if (mode === 'read') {
        disabled = true;
        object.find('button.setEditMode').show();
        object.find('button.kj_save').hide();
        object.find('button.kj_cancel').hide();
        object.find('label.kt-avatar__upload').hide();
    }
    else if (mode === 'edit') {
        disabled = false;
        object.find('button.setEditMode').hide();
        object.find('button.kj_save').show();
        object.find('button.kj_cancel').show();
        object.find('label.kt-avatar__upload').show();
    }

    inputs.attr('disabled', disabled);
    inputs.removeAttr('placeholder');

    // ScreenMode TYPES:
    // edit
    // read
    // read-hide-empty

    inputs.each(function() {

        var parent = $(this).closest('.md-form');

        if (disabled) {
            parent.find('.input-group-prepend').hide();
            parent.find('.input-group-append').hide();
        } else {
            parent.find('.input-group-prepend').show();
            parent.find('.input-group-append').show();
        }

        var screenMode = $(this).data('screen-mode');
        if ($(this).is('button')) {
            screenMode = parent.find('select').data('screen-mode');
        }

        if (screenMode !== undefined) {
            var screenModeArray = screenMode.split(',');
            screenModeArray = screenModeArray.map(Function.prototype.call, String.prototype.trim);

            var showInEdit = screenModeArray.includes('edit');
            var showInRead = screenModeArray.includes('read') || screenModeArray.includes('read-hide-empty');
            var showIfEmpty = !screenModeArray.includes('read-hide-empty');

            if (mode === 'read') {
                if (showInRead) {
                    if (showIfEmpty) {
                        if ($(this).val() === '') {
                            $(this).attr('placeholder', '-');
                        }
                        parent.show();

                        parent.find('.input-group-prepend').hide();
                        parent.find('.input-group-append').hide();
                    } else {
                        if ($(this).val() !== '') {
                            parent.show();

                            parent.find('.input-group-prepend').hide();
                            parent.find('.input-group-append').hide();
                        } else {
                            parent.hide();
                        }
                    }
                } else {
                    parent.hide();
                }
            }
            else if (mode === 'edit') {
                if (showInEdit) {
                    parent.show();

                    parent.find('.input-group-prepend').show();
                    parent.find('.input-group-append').show();
                } else {
                    parent.hide();
                }
            }
        }
    });

    setMaterialActiveLabels(object);
}

function setMaterialActiveLabels(object)
{
    $(object).find('input[type=text], input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], input[type=date], input[type=time], textarea').each(function (index) {
        if (($(this).val() !== undefined && $(this).val().length > 0) || (($(this).attr('placeholder') !== null) && ($(this).attr('placeholder') !== undefined))) {
            $(this).siblings('label').addClass('active');
        }
        else {
            $(this).siblings('label').removeClass('active');
        }
    });
}