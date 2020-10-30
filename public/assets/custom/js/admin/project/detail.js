$(document).ready(function() {

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
            if (data.success === true) {
                // When inserted then reload
                if (data.new == true) {
                    window.location = '/admin/project/detail/' + data.id;
                }

                loadScreen(container, {
                    url: '/admin/project/detailScreen',
                    mode: 'read',
                    afterLoad: afterLoadScreen
                });
            }
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
        var type = LastButton.parent().parent().find('input')[0].id;

        if(type === "REFERRER_NAME"){
            selectRelation('VERWIJZER');
        }
        if(type === "EMPLOYER_NAME"){
            selectRelation('WERKGEVER');
        }
        if(type === "PROVIDER_NAME"){
            selectRelation('PROVIDER');
        }
        if(type === "INVOICE_RELATION_NAME"){
            selectRelation('');
        }
    });

    function selectRelation(type){
        $.ajax({
            url: '/admin/crm/relation/modal?type=' + type ,
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
    }

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

    $('body').on('change', 'select[name="FK_CRM_CONTACT_EMPLOYEE"]', function(e) {
        e.preventDefault();

        $('input[name="DESCRIPTION"]').val(determineDefaultDescription());
    });

    $('body').on('change', 'input[name="COMPENSATED"]', function(e) {
        e.preventDefault();

        if (this.checked) {
            $('input[name="COMPENSATION_PERCENTAGE"]').attr('required', true);
            $('input[name="COMPENSATION_PERCENTAGE"]').attr('disabled', false);
            $('label[for="COMPENSATION_PERCENTAGE"]').text($('label[for="COMPENSATION_PERCENTAGE"]').text() + '*');
        } else {
            $('input[name="COMPENSATION_PERCENTAGE"]').removeAttr('required');
            $('input[name="COMPENSATION_PERCENTAGE"]').attr('disabled', true);
            $('label[for="COMPENSATION_PERCENTAGE"]').text($('label[for="COMPENSATION_PERCENTAGE"]').text().replace('*', ''));
        }
    });

    $('body').on('click', '#addProducts', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        var text = kjlocalization.get('admin_-_dossiers', 'selecteer_product');

        $.ajax({
            url: '/admin/product/modal?checkable=1&show_options=1',
            type: 'GET',
            dataType: 'JSON',

            success: function (data) {
                // Load detail form
                $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').text(kjlocalization.get('algemeen', 'toevoegen'));
                $('.kj_field_modal .modal-title').text(text);
                $('.kj_field_modal .modal-body').html(data.viewDetail);
                loadDatatable($('#ADM_PRODUCT_MODAL_TABLE'));
                loadDropdowns();
                loadDatePickers();

                $('.kj_field_modal').modal('show');

                setMaterialActiveLabels($('.form-group'));

                $('.kj_field_modal').off('shown.bs.modal').on('shown.bs.modal', function() {
                    ADM_PRODUCT_MODAL_TABLE_configuration.datatableSelector.redraw();
                });

                // Callback voor sluiten modal
                $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').off().on('click', function (e) {
                    e.preventDefault();

                    var productIds = getCheckedRows('ADM_PRODUCT_MODAL_TABLE');
                    var date = $('#STARTDATE').val();
                    var assignee = $('#FK_CORE_USER_ASSIGNEE').val();
                    var quatation = $('#QUOTATION_NUMBER').val();
                    if (productIds.length === 0) {
                        swal.fire({
                            text: kjlocalization.get('algemeen', 'selecteer_minimaal_een_regel'),
                            type: 'error'
                        });

                        return false;
                    }
                    if (!assignee) {
                        swal.fire({
                            text: kjlocalization.get('admin_-_taken', 'selecteer_een_werknemer'),
                            type: 'error'
                        });

                        return false;
                    }

                    var formData = new FormData();
                    formData.append('id', id);
                    formData.append('product', JSON.stringify(productIds));
                    formData.append('date', date);
                    formData.append('assignee', assignee);
                    formData.append('quatation', quatation);

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

        swal.fire({
            title: kjlocalization.get('admin_-_dossiers', 'verwijder_projectproduct_titel'),
            text: kjlocalization.get('admin_-_dossiers', 'verwijder_projectproduct_tekst'),
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: kjlocalization.get('algemeen', 'doorgaan'),
            cancelButtonText: kjlocalization.get('algemeen', 'annuleren')
        }).then(function(result) {
            if(result.value){
                kjrequest('DELETE', '/admin/project/product/' + id, null, false,
                    function(result) {
                        if (result.success) {
                            ADM_PROJECT_PRODUCTS_TABLE_configuration.datatableSelector.reload(null, false);
                        }
                    }
                );
            }
        });
    });

    //delete invoice moment of dossier
    $('body').on('click', '.deleteInvoiceMomentProject', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/invoice/scheme/project/' + id, null, false,
            function(result) {
                if (result.success) {
                    ADM_PROJECT_INVOICE_SCHEME_TABLE_configuration.datatableSelector.reload(null, false);
                }
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
    else if (screen === 'invoice_scheme') {
        loadDatatable($('#ADM_PROJECT_INVOICE_SCHEME_TABLE'));

        $('#addInvoiceMomentProject').off().on('click', function(e) {
            e.preventDefault();

            var pid = $(this).data('pid');

            // Get configuration
            var datatableName = 'ADM_PROJECT_INVOICE_SCHEME_TABLE';
            var name = datatableName + '_configuration';
            var configuration = window[name];

            var selectorName = datatableName + '-new';
            if (configuration.targetElement !== null) {
                selectorName = configuration.targetElement;
            }

            var targetElement = $('#' + selectorName);
            // Load view
            $.ajax({
                url: configuration.editURL + configuration.newrecordid + '?pid=' + pid,
                type: 'GET',
                dataType: 'JSON',

                success: function (data) {
                    // Close open rows
                    $('#'+datatableName).find('.closeRow').click();

                    // Load detail form
                    targetElement.html(data.viewDetail);

                    // Bind save events
                    if (configuration.saveUrl > '') {
                        $('#' + selectorName + ' .kj_save').on('click', function (e) {
                            e.preventDefault();
                            save($(this), configuration.saveUrl, configuration.parentid, (configuration.inlineEdit === true), targetElement, function(data) {
                                if (data.success === true) {
                                    $(document).trigger(datatableName + 'AfterSave', [data]);

                                    configuration.datatableSelector.reload(null, false)
                                }
                            });
                        });
                    }

                    // Bind cancel event
                    if (configuration.addable) {
                        $('#' + selectorName + ' .kj_cancel').on('click', function (e) {
                            e.preventDefault();

                            cancelNew(selectorName);
                        });
                    }

                    // Load fields
                    loadDropdowns();
                    loadDatePickers();
                    loadDateTimePickers();
                    loadKJPostcodeLookups();

                    setMaterialActiveLabels(targetElement);

                    // Callback to rebind events
                    $(document).trigger(datatableName + 'AfterLoad',[targetElement]);
                    $(document).trigger(datatableName + 'AfterNew',[targetElement]);

                    targetElement.slideDown(function() {
                        var firstElementje = $('#' + selectorName + ' input:not(.datepicker):not(.datetimepicker):not(.kjdaterangepicker-picker):not(:hidden)').first();
                        if (firstElementje !== undefined) {
                            firstElementje.focus();
                        }

                        $(document).trigger(datatableName + 'AfterLoadAnimation', [targetElement]);
                        $(document).trigger(datatableName + 'AfterNewAnimation', [targetElement]);
                    });
                }
            });
        });
    }
    else if (screen === 'products') {
        loadDatatable($('#ADM_PROJECT_PRODUCTS_TABLE'));

        $(document).on('ADM_PROJECT_PRODUCTS_TABLEAfterLoad', function(e, detailDiv) {
            var relationValue = $('input[name="FK_CRM_RELATION"]').val();
            $('#btnCancelQuantity').on('click', function(e) {
                $('input[name="FK_CRM_RELATION"]').val(relationValue);
            });

            $('#COMPENSATED').on('click', function(data) {
                var compensated = $('#COMPENSATED');
                var id = compensated.data('id');
                if(compensated.is(":checked")){
                    kjrequest('GET', '/admin/project/data/' + id, null, true, function (data) {
                        if( !data.item.POLICY_NUMBER || !data.item.START_DATE){
                            $.notify({message: kjlocalization.get('admin_-_dossiers', 'dossier_mist_ziektedag_of_polisnummer')}, {type: 'danger'});
                            compensated.prop( "checked", false );
                            compensated.trigger('change');
                        }
                    });
                }
            });

        });
    }
    else if (screen === 'documents') {
        loadDropzone();
    }
}

$(document).on('ADM_PROJECT_PRODUCTS_TABLERowCallback', function(e, row, data, index) {
    kjrequest('GET', '/admin/project/product/editable/'+data.ID, null, true, function (data) {
        if(data.success){
            if(!data.editable){
                row.children().last().find('span').find('span').hide();
            }
        }
    });
});

$(document).on('ADM_PROJECT_INVOICE_SCHEME_TABLERowCallback', function(e, row, data, index) {
    if (data.BLOCKED == true) {
        setTimeout(function() {
            row.children().last().find('span').find('span').hide();
        }, 10);
    }
});

function determineDefaultDescription()
{
    var employer_id = $('input[name="FK_CRM_RELATION_EMPLOYER"]').val();
    var employer = $('input[name="EMPLOYER_NAME"]').val();

    var employee_id = $('#FK_CRM_CONTACT_EMPLOYEE').val();
    var employee = $('#FK_CRM_CONTACT_EMPLOYEE').find('option[value="'+employee_id+'"]').text()

    var result = '';
    if (employer_id > 0) {
        result = employer;
        if (employee_id > 0) {
            result += ', ' + employee;
        }
    } else if (employee_id > 0) {
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