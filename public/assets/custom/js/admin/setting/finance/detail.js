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

    $('body').on('click', '.selectRelation', function(e) {
        e.preventDefault();

        LastButton = $(this);

        $.ajax({
            url: '/admin/crm/relation/modal',
            type: 'GET',
            dataType: 'JSON',

            success: function (data) {
                // Load detail form
                $('.kj_field_modal .modal-title').text(kjlocalization.get('admin_-_crm', 'selecteer_relatie'));
                $('.kj_field_modal .modal-body').html(data.viewDetail);
                loadDatatable($('#ADM_RELATION_TABLE'));
                loadDropdowns();

                // Modal showen
                $('.kj_field_modal').modal('show');
                $('.kj_field_modal').off('shown.bs.modal').on('shown.bs.modal', function() {
                    ADM_RELATION_TABLE_configuration.datatableSelector.redraw();
                    $('input[name=ADM_RELATION_FILTER_SEARCH]').focus();
                });
            }
        });
    });

    $('body').on('click', '.openRelation', function(e) {
        e.preventDefault();

        var id = $(this).closest('.md-form').next('input[type="hidden"]').val();
        if (id > 0) {
            // Open client in new window
            var win = window.open('/admin/crm/relation/detail/' + id, '_blank');
            if (win) {
                // Browser has allowed it to be opened
                win.focus();
            } else {
                // Browser has blocked it
            }

            return false;
        }
    });

    $('body').on('click', '.deletePaymentTerm', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/settings/finance/payment-term/' + id, null, false,
            function(result) {
                ADM_LABEL_PAYMENT_TERM_TABLE_configuration.datatableSelector.reload(null, false);
            }
        );
    });
});

function afterLoadScreen(id, screen, data) {
    if (screen === 'default') {
        loadUppyFileUpload($('#'+screen), ['.pdf'], '/admin/settings/finance/upload', '/admin/settings/finance/deleteFile/', '/document/request');
    }
    else if (screen === 'ledgers') {
        loadDatatable($('#ADM_LABEL_LEDGER_TABLE'));
    }
    else if (screen === 'vat') {
        loadDatatable($('#ADM_LABEL_VAT_TABLE'));
    }
    else if (screen === 'payment_term') {
        loadDatatable($('#ADM_LABEL_PAYMENT_TERM_TABLE'));
    }
}

$(document).on('ADM_RELATION_TABLEAfterSelect', function(e, selectedId, linkObj) {
    // Waardes opzoeken
    var name = linkObj.closest('tr').find('[data-field=NAME]').find('span').text();

    var id_input = LastButton.closest('.md-form').next('input[type="hidden"]');
    id_input.val(selectedId);

    var text_input = LastButton.closest('div.form-group').find('input[type=text]');
    text_input.val(name);

    // Contactpersonen refreshen
    if (text_input.data('update') !== undefined) {
        $.ajax({
            url: '/admin/crm/relation/contact/allByRelation/' + selectedId,
            type: 'GET',
            dataType: 'JSON',

            success: function (data) {
                var updatedValues = text_input.data('update').split(',');
                $.each(updatedValues, function (index, value) {
                    var select = $('#' + value);

                    select.empty();
                    $.each(data.items, function (index, value) {
                        select.append($("<option></option>").attr("value", index).text(value));
                    });

                    select.val('');
                    select.selectpicker('refresh');
                });
            }
        });
    }

    // Modal hidden
    $('.kj_field_modal').modal('hide');
});