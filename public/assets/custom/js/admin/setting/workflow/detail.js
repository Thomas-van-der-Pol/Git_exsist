$(document).ready(function() {

    kjlocalization.create('Admin - Dossiers', [
        {'Selecteer product': 'Selecteer product'}
    ]);

    // Load screen
    loadScreen($('#default'), {
        url: '/admin/settings/workflow/detailScreen',
        mode: 'read',
        afterLoad: afterLoadScreen
    });

    // Load active sub screen
    $('.kt-widget__item--active').each(function() {
        loadScreen($(this), {
            url: '/admin/settings/workflow/detailScreen',
            afterLoad: afterLoadScreen
        });
    });

    // Switch tab action
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        loadScreen($(e.target), {
            url: '/admin/settings/workflow/detailScreen',
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
    $('body').on('click', '#btnCancelWorkflow', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        setScreenMode(container, 'read');
    });

    // Save action
    $('body').on('click', '#btnSaveWorkflow, #btnSaveWorkflowNew', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        save($(this), '/admin/settings/workflow', null, false, null, function(data) {
            // When inserted then reload
            if (data.new == true) {
                window.location = '/admin/settings/workflow/detail/' + data.id;
            }

            loadScreen(container, {
                url: '/admin/settings/workflow/detailScreen',
                mode: 'read',
                afterLoad: afterLoadScreen
            });
        });
    });

    $('body').on('click', '#btnCancelWorkflowNew', function(e) {
        e.preventDefault();

        window.location = '/admin/settings/workflow';
    });

    $('body').on('click', '.activateItem', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/settings/workflow/' + id, null, false,
            function(result) {
                if (result.success) {
                    loadScreen($('#default'), {
                        url: '/admin/settings/workflow/detailScreen',
                        mode: 'read',
                        afterLoad: afterLoadScreen
                    });
                }
            }
        );
    });

    $('body').on('click', '.deleteState', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/settings/workflow/state/' + id, null, false,
            function(result) {
                if (result.success) {
                    loadScreen($('#default'), {
                        url: '/admin/settings/workflow/detailScreen',
                        mode: 'read',
                        afterLoad: afterLoadScreen
                    });

                    ADM_WORKFLOW_STATE_TABLE_configuration.datatableSelector.reload(null, false);
                }
            }
        );
    });

    $('body').on('click', '#addProducts', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        var type = $(this).data('type');

        $.ajax({
            url: '/admin/product/modal?checkable=1&type=' + type,
            type: 'GET',
            dataType: 'JSON',

            success: function (data) {
                // Load detail form
                $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').text(kjlocalization.get('algemeen', 'toevoegen'));
                $('.kj_field_modal .modal-title').text(kjlocalization.get('admin_-_dossiers', 'selecteer_dienst'));
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
                        url: '/admin/settings/workflow/product/addProduct',
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        data: formData,

                        success: function (data) {
                            if (data.success === true) {
                                // Reload datatable
                                ADM_WORKFLOW_PRODUCTS_TABLE_configuration.datatableSelector.reload(null, false);

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
        kjrequest('DELETE', '/admin/settings/workflow/product/' + id, null, false,
            function(result) {
                if (result.success) {
                    ADM_WORKFLOW_PRODUCTS_TABLE_configuration.datatableSelector.reload(null, false);
                }
            }
        );
    });

});

function afterLoadScreen(id, screen, data) {
    if (screen === 'states') {
        loadDatatable($('#ADM_WORKFLOW_STATE_TABLE'));
    }
    else if (screen === 'services') {
        loadDatatable($('#ADM_WORKFLOW_PRODUCTS_TABLE'));
    }
}

$(document).on('ADM_WORKFLOW_STATE_TABLEAfterReorder', function(e, item, id, currentSequence, oldPosition, newPosition, mutation) {
    processSequence(
        item,
        id,
        mutation,
        '/admin/settings/workflow/state/sequence/',
        ADM_WORKFLOW_STATE_TABLE_configuration
    );
});

$(document).on('ADM_WORKFLOW_STATE_TABLEAfterSave', function(e, data) {
    if (data.success === true) {
        loadScreen($('#default'), {
            url: '/admin/settings/workflow/detailScreen',
            mode: 'read',
            afterLoad: afterLoadScreen
        });
    }
});


function processSequence(item, id, mutation, url, datatableConfig)
{
    var currentProductSequence = item.data('sequence');
    var workflowId = $('input[name=ID]').val();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: url + workflowId,
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': id,
            'currentSequence': currentProductSequence,
            'mutation':  mutation
        },
        success: function (data) {
            if (!data.success) {
                $.notify({message: data.message}, {type: 'danger'});
            }
            datatableConfig.datatableSelector.reload(null, false);
        }
    });
}