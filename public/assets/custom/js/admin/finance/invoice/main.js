var loadedType = null;

$(document).ready(function() {
    loadDateRangePickers();

    // Translations
    kjlocalization.create('Admin - Facturen', [
        {'Geen regels geselecteerd': 'Geen regels geselecteerd'},
        {'Bericht selectie factureren': 'Weet je zeker dat je deze facturen definitief wil versturen? Dit actie is niet ongedaan te maken.'},
        {'Bericht selectie herinnering': 'Weet je zeker dat je deze facturen een herinnering wil versturen? Dit actie is niet ongedaan te maken.'},
    ]);

    // Load active sub screen
    $('.kt-widget__item--active').each(function() {
        var screen = $(this).attr('href').replace('#', '');
        var type = $('#'+screen).data('type');

        loadScreen($(this), {
            url: '/admin/invoice/detailScreenOverview',
            afterLoad: afterLoadScreen,
            postData: [
                {'type': type},
            ]
        });
    });

    // Switch tab action
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        var screen = $(this).attr('href').replace('#', '');
        var type = $('#'+screen).data('type');

        storeSession('ADM_INVOICE', 'CURRENT_TAB', type);

        loadScreen($(e.target), {
            url: '/admin/invoice/detailScreenOverview',
            afterLoad: afterLoadScreen,
            postData: [
                {'type': type},
            ]
        });
    });

    $('body').on('click', '#sendBulkInvoice', function(e) {
        e.preventDefault();

        var ids = getCheckedRows('ADM_INVOICE_TABLE_' + loadedType);
        if(ids.length == 0) {
            $.notify({message: kjlocalization.get('admin_-_facturen', 'geen_regels_geselecteerd')}, {type: 'danger'});
            return;
        }

        var formData = new FormData();
        formData.append('ids', JSON.stringify(ids));

        // Confirmation vragen
        swal.fire({
            text: kjlocalization.get('admin_-_facturen', 'bericht_selectie_factureren'),
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: kjlocalization.get('algemeen', 'doorgaan'),
            cancelButtonText: kjlocalization.get('algemeen', 'annuleren')
        }).then(function(result) {
            if (result.value) {
                startKJLoader({funVersion: true, CrazyLoader: false});

                kjrequest('POST', '/admin/invoice/generateBulk', formData, true,
                    function(data) {
                        if (data.success === true)
                        {
                            stopKJLoader();
                            swal.fire({
                                text: data.message,
                                type: "success",
                                timer: 1500
                            }).then(function(result) {
                                var configuration = window['ADM_INVOICE_TABLE_' + loadedType + '_configuration'];
                                configuration.datatableSelector.reload(null, false);
                            });
                        }
                        else
                        {
                            stopKJLoader();
                            swal.fire({
                                text: data.message,
                                type: 'error'
                            });
                        }
                    },
                    function() {
                        stopKJLoader();
                    }
                );

            } else if (result.dismiss === 'cancel') {
                return false;
            }
        });
    });

    $('body').on('click', '#sendBulkReminder', function(e) {
        e.preventDefault();

        var ids = getCheckedRows('ADM_INVOICE_TABLE_' + loadedType);
        if(ids.length == 0) {
            $.notify({message: kjlocalization.get('admin_-_facturen', 'geen_regels_geselecteerd')}, {type: 'danger'});
            return;
        }

        var formData = new FormData();
        formData.append('ids', JSON.stringify(ids));

        // Confirmation vragen
        swal.fire({
            text: kjlocalization.get('admin_-_facturen', 'bericht_selectie_herinnering'),
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: kjlocalization.get('algemeen', 'doorgaan'),
            cancelButtonText: kjlocalization.get('algemeen', 'annuleren')
        }).then(function(result) {
            if (result.value) {
                startKJLoader({funVersion: true, CrazyLoader: false});

                kjrequest('POST', '/admin/invoice/reminderBulk', formData, true,
                    function(data) {
                        if (data.success === true)
                        {
                            stopKJLoader();
                            swal.fire({
                                text: data.message,
                                type: "success",
                                timer: 1500
                            }).then(function(result) {
                                var configuration = window['ADM_INVOICE_TABLE_' + loadedType + '_configuration'];
                                configuration.datatableSelector.reload(null, false);
                            });
                        }
                        else
                        {
                            stopKJLoader();
                            swal.fire({
                                text: data.message,
                                type: 'error'
                            });
                        }
                    },
                    function() {
                        stopKJLoader();
                    }
                );

            } else if (result.dismiss === 'cancel') {
                return false;
            }
        });
    });
});

function afterLoadScreen(id, screen, data) {
    loadedType = data.type;

    // Set visible buttons based on type
        // Selectie factureren
        // - Concept
        if (screen === 'concept_invoices') {
            $('#sendBulkInvoice').show();
            $('#newInvoice').show();
        } else {
            $('#sendBulkInvoice').hide();
            $('#newInvoice').hide();
        }

        // Selectie herinnering versturen
        // - Openstaand
        // - Vervallen
        if ((screen === 'open_invoices') || (screen === 'expired_invoices')) {
            $('#sendBulkReminder').show();
        } else {
            $('#sendBulkReminder').hide();
        }

    // Load datatable
    loadDatatable($('#ADM_INVOICE_TABLE_'+data.type));

    // @TODO: LOAD DATATABLE MOET ONDERSTAANDE WORDEN!!!!

    // var configName = 'ADM_INVOICE_TABLE_' + data.type;
    // var configuration = window[configName + '_configuration'];
    //
    // if (configuration.datatableSelector === undefined) {
    //     // Add filters
    //     configuration.source.params = {
    //         ADM_PROJECT_FILTER_SEARCH: $('#ADM_PROJECT_FILTER_SEARCH').val() || '',
    //         ACTIVE: $('#ADM_FILTER_PROJECT_STATUS').val() || ''
    //     };
    //
    //     // Remove empty filters from arrays
    //     $.each(configuration.source.params, function (k, v) {
    //         if (v === '' || $.isEmptyObject(v)) {
    //             delete configuration.source.params[k];
    //         }
    //     });
    //
    //     // Load datatable
    //     loadDatatable($('#'+configName));
    // } else {
    //     // Retrieve query
    //     var query = configuration.datatableSelector.getDataSourceQuery();
    //
    //     // Set filters manually
    //     query['ADM_PROJECT_FILTER_SEARCH'] = $('#ADM_PROJECT_FILTER_SEARCH').val() || '';
    //     query['ACTIVE'] = $('#ADM_FILTER_PROJECT_STATUS').val() || '';
    //
    //     // remove empty element from arrays
    //     $.each(query, function (k, v) {
    //         if (v === '' || $.isEmptyObject(v)) {
    //             delete query[k];
    //         }
    //     });
    //
    //     // Set filters on datatable
    //     configuration.datatableSelector.setDataSourceQuery(query);
    //
    //     // Reload datatable
    //     configuration.datatableSelector.dataRender();
    // }
}