$(document).ready(function() {

    kjlocalization.create('Admin - Producten & diensten', [
        {'Selecteer product': 'Selecteer product'}
    ]);

    kjlocalization.create('Admin - CRM', [
        {'Selecteer relatie': 'Selecteer relatie'},
        {'Selecteer contactpersoon': 'Selecteer contactpersoon'}
    ]);

    // Load screen
    loadScreen($('#default'), {
        url: '/admin/project/detailScreen',
        mode: 'read',
        afterLoad: afterLoadScreen
    });

    // Load active sub screen
    $('.kt-widget__item--active').each(function() {
        loadScreen($(this), {
            url: '/admin/project/detailScreen',
            afterLoad: afterLoadScreen
        });
    });

    // Switch tab action
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        loadScreen($(e.target), {
            url: '/admin/project/detailScreen',
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
    $('body').on('click', '#btnCancelProject', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        setScreenMode(container, 'read');
    });

    // Save action
    $('body').on('click', '#btnSaveProject, #btnSaveProjectNew', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        save($(this), '/admin/project', null, false, null, function(data) {
            // When inserted then reload
            if (data.new == true) {
                window.location = '/admin/project/detail/' + data.id;
            }

            loadScreen(container, {
                url: '/admin/project/detailScreen',
                mode: 'read',
                afterLoad: afterLoadScreen
            });
        });
    });

    $('body').on('click', '#btnCancelProjectNew', function(e) {
        e.preventDefault();

        window.location = '/admin/project';
    });

    $('body').on('click', '.activateItem', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/project/' + id, null, false,
            function(result) {
                if (result.success) {
                    loadScreen($('#default'), {
                        url: '/admin/project/detailScreen',
                        mode: 'read',
                        afterLoad: afterLoadScreen
                    });
                }
            }
        );
    });

    $('body').on('click', '.processWorkflowstate', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        var state = $(this).data('state');

        var formData = new FormData();
        formData.append('ID', id);
        formData.append('FK_CORE_WORKFLOWSTATE', state);

        kjrequest('POST', '/admin/project', formData, false,
            function(result) {
                if (result.success) {
                    loadScreen($('#default'), {
                        url: '/admin/project/detailScreen',
                        mode: 'read',
                        afterLoad: afterLoadScreen
                    });
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

    $('body').on('click', '.selectContact', function(e) {
        e.preventDefault();

        LastButton = $(this);

        $.ajax({
            url: '/admin/crm/contact/modal',
            type: 'GET',
            dataType: 'JSON',

            success: function (data) {
                // Load detail form
                $('.kj_field_modal .modal-title').text(kjlocalization.get('admin_-_crm', 'selecteer_contactpersoon'));
                $('.kj_field_modal .modal-body').html(data.viewDetail);
                loadDatatable($('#ADM_CRM_CONTACT_MODAL_TABLE'));
                loadDropdowns();

                // Modal showen
                $('.kj_field_modal').modal('show');
                $('.kj_field_modal').off('shown.bs.modal').on('shown.bs.modal', function() {
                    ADM_CRM_CONTACT_MODAL_TABLE_configuration.datatableSelector.redraw();
                    $('input[name=ADM_CRM_CONTACT_FILTER_SEARCH]').focus();
                });
            }
        });
    });

    $('body').on('change', 'select[name="FK_CRM_CONTACT_EMPLOYER"]', function(e) {
        e.preventDefault();

        $('input[name="DESCRIPTION"]').val(determineDefaultDescription());
    });

    $('body').on('click', '#addProducts', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        var type = $(this).data('type');
        var text = kjlocalization.get('admin_-_producten_&_diensten', 'selecteer_product');

        $.ajax({
            url: '/admin/product/modal?checkable=1&type=' + type,
            type: 'GET',
            dataType: 'JSON',

            success: function (data) {
                // Load detail form
                $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').text(kjlocalization.get('algemeen', 'toevoegen'));
                $('.kj_field_modal .modal-title').text(text);
                $('.kj_field_modal .modal-body').html(data.viewDetail);
                loadDatatable($('#ADM_PRODUCT_MODAL_TABLE'));
                loadDropdowns();

                $('.kj_field_modal').modal('show');

                $('.kj_field_modal').off('shown.bs.modal').on('shown.bs.modal', function() {
                    ADM_PRODUCT_MODAL_TABLE_configuration.datatableSelector.redraw();
                });

                // Callback voor sluiten modal
                $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').off().on('click', function (e) {
                    e.preventDefault();

                    var productIds = getCheckedRows('ADM_PRODUCT_MODAL_TABLE');

                    if (productIds.length === 0) {
                        swal.fire({
                            text: kjlocalization.get('algemeen', 'selecteer_minimaal_een_regel'),
                            type: 'error'
                        });

                        return false;
                    }

                    var formData = new FormData();
                    formData.append('id', id);
                    formData.append('product', JSON.stringify(productIds));

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: '/admin/project/product/addProduct',
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        data: formData,

                        success: function (data) {
                            if (data.success === true) {
                                // Reload datatable
                                ADM_PROJECT_PRODUCTS_TABLE_configuration.datatableSelector.reload(null, false);

                                // Modal hidden
                                $('.kj_field_modal').modal('hide');
                            }
                        }
                    });
                });
            }
        });
    });

    $('body').on('click', '.deleteProduct', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        var type = $(this).data('type');
        kjrequest('DELETE', '/admin/project/product/' + id, null, false,
            function(result) {
                if (result.success) {
                    ADM_PROJECT_PRODUCTS_TABLE_configuration.datatableSelector.reload(null, false);
                }
            }
        );
    });

    // Invoices
    $('body').on('click', '#addAdvanceInvoice', function(e) {
        e.preventDefault();

        // Set edit mode
        setScreenMode($('#advance_invoice_modal'), 'edit');

        // Show modal
        $('.advance_error').hide();
        $('#advance_invoice_modal').modal('show');
    });

    $('body').on('change', '#ADVANCE_TYPE', function(e) {
        e.preventDefault();

        $('.advance_show_at_type').hide();
        $('.advance_show_at_type[data-type="'+$(this).val()+'"]').show();

        countAdvanceAmount();
    });

    $('body').on('change', 'input[name=ADVANCE_PERCENTAGE], input[name=ADVANCE_AMOUNT]', function(e) {
        e.preventDefault();

        countAdvanceAmount();
    });

    $('body').on('click', '#createAdvanceInvoice', function(e) {
        e.preventDefault();

        var form = $('#advance_invoice');

        var formData = new FormData();
        formData.append('ID', form.find('input[name=ID]').val());
        formData.append('TYPE', form.find('#ADVANCE_TYPE').val());
        formData.append('PERCENTAGE', form.find('input[name=ADVANCE_PERCENTAGE]').val() || 0);
        formData.append('AMOUNT', form.find('input[name=ADVANCE_AMOUNT]').val() || 0);

        kjrequest('POST', '/admin/invoice/createAdvance', formData, true,
            function (data) {
                if (data.success === true) {
                    window.location = '/admin/invoice/detail/' + data.id;
                } else {
                    $.notify({message: data.message},{type: 'danger', z_index: 99999});
                }

                $('#advance_invoice_modal').modal('hide');
            }
        );
    });

    $('body').on('kt-datatable--on-ajax-done', '#ADM_PROJECT_PRODUCTS_TABLE', function() {
        var project_id = $('input[name=ID]').val();

        kjrequest('GET', '/admin/project/product/allByProjectProductTotal/' + project_id, null, true,
            function (data) {
                $('#productTotal').text(data.total);
            }
        );
    });
});

function afterLoadScreen(id, screen, data) {
    if (screen === 'default') {
        $('#FK_PROJECT_INVOICE_TYPE').trigger('change');
    }
    else if (screen === 'tasks') {
        loadTaskScreen($('#'+screen));
    }
    else if (screen === 'invoices') {
        loadDatatable($('#ADM_PROJECT_INVOICE_TABLE'));
    }
    else if (screen === 'products') {
        loadDatatable($('#ADM_PROJECT_PRODUCTS_TABLE'));
    }
    else if (screen === 'guidelines') {
        // Load uppy only for downloading files
        loadUppyFileUpload($('#' + screen), ['.pdf', '.docx'], '', '', '/document/request');
    }
    else if (screen === 'documents') {
        loadDropzone();
    }
}

function determineDefaultDescription()
{
    var employer_id = $('input[name="FK_CRM_RELATION_EMPLOYER"]').val();
    var employer = $('input[name="EMPLOYER_NAME"]').val();

    var employer_contact_id = $('select[name="FK_CRM_CONTACT_EMPLOYER"]').val();
    var employer_contact = $('select[name="FK_CRM_CONTACT_EMPLOYER"] option:selected').text() || '';

    var employee = $('input[name="EMPLOYEE_NAME"]').val();

    var result = '';
    if (employer_id > 0) {
        result = employer;
        if (employer_contact_id > 0) {
            result += ', ' + employer_contact;
        }
    } else {
        result = employee;
    }

    return result;
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
                var select =  $('#'+text_input.data('update'));

                select.empty();
                $.each(data.items, function (index, value) {
                    select.append($("<option></option>").attr("value", index).text(value));
                });

                select.val('');
                select.selectpicker('refresh');
            }
        });
    }

    // Determine description field
    $('input[name="DESCRIPTION"]').val(determineDefaultDescription());

    // Modal hidden
    $('.kj_field_modal').modal('hide');
});

$(document).on('ADM_CRM_CONTACT_MODAL_TABLEAfterSelect', function(e, selectedId, linkObj) {
    // Waardes opzoeken
    var name = linkObj.closest('tr').find('[data-field=FULLNAME]').find('span').text();

    var id_input = LastButton.closest('.md-form').next('input[type="hidden"]');
    id_input.val(selectedId);

    var text_input = LastButton.closest('div.form-group').find('input[type=text]');
    text_input.val(name);

    // Determine description field
    $('input[name="DESCRIPTION"]').val(determineDefaultDescription());

    // Modal hidden
    $('.kj_field_modal').modal('hide');
});

function countAdvanceAmount()
{
    var type = $('#ADVANCE_TYPE').val();
    var maxAmount = parseFloat($('input[name="MAX_AMOUNT"]').val());
    var amount = 0;

    // Percentage
    if (type == 1) {
        var percentage = parseFloat($('input[name=ADVANCE_PERCENTAGE]').val()) || 0;
        if (percentage > 100) {
            // Error
            $('.advance_error').show();
            amount = 0;
        } else {
            $('.advance_error').hide();
            amount = maxAmount * (percentage / 100);
        }
    }
    // Vast bedrag
    else if (type == 2) {
        var fixedAmount = parseFloat($('input[name=ADVANCE_AMOUNT]').val()) || 0;
        if (fixedAmount > maxAmount) {
            // Error
            $('.advance_error').show();
            amount = 0;
        } else {
            $('.advance_error').hide();
            amount = fixedAmount;
        }
    }

    $('#advance_summary_amount').text('€ ' + amount);
}