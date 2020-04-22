$(document).ready(function() {
    kjlocalization.create('Admin - CRM', [
        {'Opslaan + taak': 'Opslaan + taak'},
        {'Emailadres leeg': 'Emailadres leeg'},
        {'Wachtwoord succesvol verzonden': 'Wachtwoord succesvol verzonden'}
    ]);

    kjlocalization.create('Admin - Dossiers', [
        {'Selecteer product': 'Selecteer product'},
        {'Selecteer dienst': 'Selecteer dienst'}
    ]);

    // Load screen
    loadScreen($('#default'), {
        url: '/admin/crm/relation/detailScreen',
        mode: 'read',
        afterLoad: afterLoadScreen
    });

    // Load active sub screen
    $('.kt-widget__item--active').each(function() {
        loadScreen($(this), {
            url: '/admin/crm/relation/detailScreen',
            afterLoad: afterLoadScreen
        });
    });

    // Switch tab action
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        loadScreen($(e.target), {
            url: '/admin/crm/relation/detailScreen',
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
    $('body').on('click', '#btnCancelRelation, #btnCancelFinancialDetails', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        setScreenMode(container, 'read');
    });

    // Save action
    $('body').on('click', '#btnSaveRelation, #btnSaveRelationNew, #btnSaveFinancialDetails', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        save($(this), '/admin/crm/relation', null, false, null, function(data) {
            // When inserted then reload
            if (data.new == true) {
                window.location = '/admin/crm/relation/detail/' + data.id;
            }

            loadScreen(container, {
                url: '/admin/crm/relation/detailScreen',
                mode: 'read',
                afterLoad: afterLoadScreen
            });
        });
    });

    $('body').on('click', '#btnCancelRelationNew', function(e) {
        e.preventDefault();

        window.location = '/admin/crm/relation';
    });

    $('body').on('click', '.activateItem', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/crm/relation/' + id, null, false,
            function(result) {
                if (result.success) {
                    loadScreen($('#default'), {
                        url: '/admin/crm/relation/detailScreen',
                        mode: 'read',
                        afterLoad: afterLoadScreen
                    });
                }
            }
        );
    });


    $('body').on('click', '.copyAddress', function(e) {
        e.preventDefault();

        var id = $(this).data('id');

        var formData = new FormData();
        formData.append('id', id);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/admin/crm/address/replicate',
            type: 'POST',
            contentType: false,
            processData: false,
            data: formData,

            success: function(data) {
                if (data.success === true) {
                    // Reload datatable
                    ADM_RELATION_ADDRESS_TABLE_configuration.datatableSelector.reload(null, false);
                }
            }
        });
    });

    $('body').on('click', '.deleteAddress', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/crm/address/' + id, null, false,
            function(result) {
                if (result.success) {
                    ADM_RELATION_ADDRESS_TABLE_configuration.datatableSelector.reload(null, false);
                }
            }
        );
    });

    $('body').on('click', '.anonymizeContact', function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        initAnonymizeFunction(id);
    });

    $('body').on('click', '.generateDebtornumber', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        var id = $(this).data('id');
        kjrequest('POST', '/admin/crm/relation/debtor/' + id, null, true,
            function(result) {
                if (result.success) {
                    loadScreen(container, {
                        url: '/admin/crm/relation/detailScreen',
                        mode: 'read',
                        afterLoad: afterLoadScreen
                    });
                }
            }
        );
    });
});

function afterLoadScreen(id, screen, data) {
    if (screen === 'contacts') {
        loadDatatable($('#ADM_RELATION_CONTACT_TABLE'));
    }
    else if (screen === 'addresses') {
        loadDatatable($('#ADM_RELATION_ADDRESS_TABLE'));
    }
    else if (screen === 'projects') {
        loadDatatable($('#ADM_RELATION_PROJECT_TABLE'));
    }
    else if (screen === 'documents') {
        loadDropzone();
    }
    else if (screen === 'guidelines') {
        loadDatatable($('#ADM_RELATION_GUIDELINE_TABLE'));
    }
    else if (screen === 'invoices') {
        loadDatatable($('#ADM_RELATION_INVOICE_TABLE'));
    }
    else if (screen === 'tasks') {
        loadTaskScreen($('#'+screen));
    }
}

$(document).on('ADM_PRODUCT_MODAL_TABLEAfterSelect', function(e, selectedId, linkObj) {
    // Waardes opzoeken
    var name = linkObj.closest('tr').find('[data-field=DESCRIPTION_INT]').find('span').text();

    var id_input = LastButton.closest('form').find('input[name=FK_ASSORTMENT_PRODUCT]');
    id_input.val(selectedId);

    var text_input = LastButton.closest('div.form-group').find('input[type=text]');
    text_input.val(name);

    //Ophalen extra gegevens met AJAX
    $.ajax({
        url: '/admin/product/data/'+selectedId,
        type: 'GET',
        contentType: false,
        processData: false,

        success: function (data) {
            $('input[name=PRICE]').val(data.PriceDecimal);

            $('select[name=FK_FINANCE_LEDGER]').val(data.FK_FINANCE_LEDGER);
            $('select[name=FK_FINANCE_LEDGER]').selectpicker('refresh');

            $('select[name=FK_FINANCE_VAT]').val(data.FK_FINANCE_VAT);
            $('select[name=FK_FINANCE_VAT]').selectpicker('refresh');

            setMaterialActiveLabels(LastButton.closest('div.kt-portlet__body'));
        }
    });

    // Modal hidden
    $('.kj_field_modal').modal('hide');
});

$(document).on('ADM_RELATION_CONTACT_TABLEAfterSave', function(e, data) {
    if (data.success === true) {
        loadScreen($('#default'), {
            url: '/admin/crm/relation/detailScreen',
            mode: 'read',
            afterLoad: afterLoadScreen
        });
    }
});