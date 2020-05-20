$(document).ready(function() {
    // Load screen
    loadScreen($('#default'), {
        url: '/admin/product/detailScreen',
        mode: 'read',
        afterLoad: afterLoadScreen
    });

    // Load active sub screen
    $('.kt-widget__item--active').each(function() {
        loadScreen($(this), {
            url: '/admin/product/detailScreen',
            afterLoad: afterLoadScreen
        });
    });

    // Switch tab action
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        loadScreen($(e.target), {
            url: '/admin/product/detailScreen',
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
    $('body').on('click', '#btnCancelProduct', function(e) {
        setScreenMode($('#default'), 'read');
    });

    $('body').on('click', '#btnSaveProduct, #btnSaveProductNew', function(e) {
        e.preventDefault();

        save($(this), '/admin/product', null, false, null, function(data) {
            if (data.success === true) {
                // When inserted then reload
                if (data.new == true) {
                    window.location = '/admin/product/detail/' + data.id;
                }

                loadScreen($('#default'), {
                    url: '/admin/product/detailScreen',
                    mode: 'read',
                    afterLoad: afterLoadScreen
                });
            }
        });
    });

    $('body').on('click', '#btnCancelProductNew', function(e) {
        e.preventDefault();

        window.location = '/admin/product';
    });

    $('body').on('click', '.activateItem', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/product/' + id, null, false,
            function(result) {
                if (result.success) {
                    loadScreen($('#default'), {
                        url: '/admin/product/detailScreen',
                        mode: 'read',
                        afterLoad: afterLoadScreen
                    });
                }
            }
        );
    });

    $('body').on('click', '.deleteInvoiceMoment', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/invoice/scheme/' + id, null, false,
            function(result) {
                if (result.success) {
                    ADM_PRODUCT_INVOICE_SCHEME_TABLE_configuration.datatableSelector.reload(null, false);
                }
            }
        );
    });
});

function afterLoadScreen(id, screen, data) {
    if (screen === 'default') {
        loadDropdowns();
    } else if (screen === 'invoice_scheme') {
        loadDatatable($('#ADM_PRODUCT_INVOICE_SCHEME_TABLE'));
    }
    else if (screen === 'tasks') {
        loadTaskScreen($('#'+screen));
    }
}