var loadedType = null;

$(document).ready(function() {
    loadDateRangePickers();

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

                            // Check printable invoices
                            if (data.print_invoices.length > 0)
                            {
                                setTimeout(function () {
                                    // Confirmation vragen
                                    swal.fire({
                                        text: kjlocalization.get('admin_-_facturen', 'bericht_selectie_printen'),
                                        type: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: kjlocalization.get('algemeen', 'doorgaan'),
                                        cancelButtonText: kjlocalization.get('algemeen', 'annuleren')
                                    }).then(function(result) {
                                        if (result.value) {
                                            if (kjcommunicator.installed === true) {
                                                kjcommunicator.getComputer(function (communicatorData) {
                                                    if ((communicatorData.name !== '') && (communicatorData.name !== undefined)) {
                                                        var subFormData = new FormData();
                                                        subFormData.append('name', communicatorData.name);
                                                        subFormData.append('mac', communicatorData.macAddress);

                                                        // Juiste printer ophalen
                                                        kjrequest('POST', '/admin/settings/host/getPrintersByHost', subFormData, true,
                                                            function(printerData) {
                                                                var printer = printerData.host.PRINTER_INVOICE;

                                                                // Loading
                                                                startKJLoader({
                                                                    funVersion: true,
                                                                    CrazyLoader: true,
                                                                    customContent: kjlocalization.get('erp_communicator', 'melding_genereren')
                                                                });

                                                                if ((printer !== '') && (printer !== undefined)) {

                                                                    $.each(data.print_invoices, function(index, document) {
                                                                        kjcommunicator.printDocument(
                                                                            printer,
                                                                            document.url,
                                                                            document.fileRequest.TOKEN,
                                                                            1
                                                                        );
                                                                    });
                                                                } else {
                                                                    swal.fire({
                                                                        text: kjlocalization.get('erp_facturen', 'printer_niet_ingesteld'),
                                                                        type: 'error'
                                                                    }).then(function() {
                                                                        $.each(data.print_invoices, function(index, document) {
                                                                            downloadFileAjax('GET', '/document/download?token=' + document.fileRequest.TOKEN);
                                                                        });
                                                                    });
                                                                }

                                                                stopKJLoader();
                                                            },
                                                            null
                                                        );
                                                    } else {
                                                        swal.fire({
                                                            text: kjlocalization.get('erp_communicator', 'bericht_communicator_fout'),
                                                            type: 'error'
                                                        });
                                                    }
                                                });
                                            } else {
                                                $.each(data.print_invoices, function(index, document) {
                                                    downloadFileAjax('GET', '/document/download?token=' + document.fileRequest.TOKEN);
                                                });
                                            }
                                        } else if (result.dismiss === 'cancel') {
                                            // return false;
                                        }

                                        // Refresh table
                                        var configuration = window['ADM_INVOICE_TABLE_' + loadedType + '_configuration'];
                                        configuration.datatableSelector.reload(null, false);
                                    });
                                }, 500);
                            } else {
                                // Refresh table
                                swal.fire({
                                    text: data.message,
                                    type: "success",
                                    timer: 1500
                                }).then(function (result) {
                                    var configuration = window['ADM_INVOICE_TABLE_' + loadedType + '_configuration'];
                                    configuration.datatableSelector.reload(null, false);
                                });
                            }
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
}