var logo_email_select;

$(document).ready(function() {

    // Load screen
    loadScreen($('#default'), {
        url: '/admin/settings/finance/detailScreen',
        mode: 'read',
        afterLoad: afterLoadScreen
    });

    // Load active sub screen
    $('.kt-widget__item--active').each(function() {
        loadScreen($(this), {
            url: '/admin/settings/finance/detailScreen',
            afterLoad: afterLoadScreen
        });
    });

    // Switch tab action
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        loadScreen($(e.target), {
            url: '/admin/settings/finance/detailScreen',
            afterLoad: afterLoadScreen
        });
    });

    // Edit action
    $('body').on('click', '.setEditMode', function(e) {
        e.preventDefault();

        var target = $(this).data('target');
        setScreenMode($('#' + target), 'edit');
    });

    // Cancel action
    $('body').on('click', '#btnCancelLabel, #btnCancelLabelSettings', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        setScreenMode(container, 'read');
    });

    // Save action
    $('body').on('click', '#btnSaveLabel, #btnSaveLabelNew, #btnSaveLabelSettings', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        save($(this), '/admin/settings/finance', null, false, null, function(data) {
            if (data.success === true) {
                // When inserted then reload
                if (data.new == true) {
                    window.location = '/admin/settings/finance/detail/' + data.id;
                }

                loadScreen(container, {
                    url: '/admin/settings/finance/detailScreen',
                    mode: 'read',
                    afterLoad: afterLoadScreen
                });
            }
        });
    });

    $('body').on('click', '#btnCancelLabelNew', function(e) {
        e.preventDefault();

        window.location = '/admin/settings/finance';
    });

    $('body').on('click', '.activateItem', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/settings/finance/' + id, null, false,
            function(result) {
                if (result.success) {
                    loadScreen($('#default'), {
                        url: '/admin/settings/finance/detailScreen',
                        mode: 'read',
                        afterLoad: afterLoadScreen
                    });
                }
            }
        );
    });

    $('body').on('click', '.deleteLedger', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/settings/finance/ledger/' + id, null, false,
            function(result) {
                if (result.success) {
                    ADM_LABEL_LEDGER_TABLE_configuration.datatableSelector.reload(null, false);
                }
            }
        );
    });

    $('body').on('click', '.deleteVat', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/settings/finance/vat/' + id, null, false,
            function(result) {
                if (result.success) {
                    ADM_LABEL_VAT_TABLE_configuration.datatableSelector.reload(null, false);
                }
            }
        );
    });

    $('body').on('click', '.deleteOverheadCharge', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/settings/finance/overhead_charge/' + id, null, false,
            function(result) {
                if (result.success) {
                    ADM_LABEL_OVERHEAD_CHARGE_TABLE_configuration.datatableSelector.reload(null, false);
                }
            }
        );
    });
});

function afterLoadScreen(id, screen, data) {
    if (screen === 'default') {
        logo_email_select = new KTAvatar('LOGO_EMAIL_SELECT');
        loadUppyFileUpload($('#'+screen), ['.pdf'], '/admin/settings/finance/upload', '/admin/settings/finance/deleteFile/', '/document/request');
    }
    else if (screen === 'ledgers') {
        loadDatatable($('#ADM_LABEL_LEDGER_TABLE'));
    }
    else if (screen === 'vat') {
        loadDatatable($('#ADM_LABEL_VAT_TABLE'));
    }
    else if (screen === 'overhead_charge') {
        loadDatatable($('#ADM_LABEL_OVERHEAD_CHARGE_TABLE'));
    }
}