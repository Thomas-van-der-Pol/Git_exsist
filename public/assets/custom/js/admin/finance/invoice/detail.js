$(document).ready(function() {

    // Translations
    kjlocalization.create('Admin - Dossiers', [
        {'Selecteer product': 'Selecteer product'}
    ]);

    kjlocalization.create('Admin - Facturen', [
        {'Bericht status wijziging': 'Weet u zeker dat u door wilt gaan?'},
        {'Bericht versturen': 'Weet u zeker dat u deze factuur defintief wilt verzenden? Deze actie is niet ongedaan te maken.'},
        {'Bericht versturen herinnering': 'Weet u zeker dat u een herinnering van deze factuur wilt verzenden? Deze actie is niet ongedaan te maken.'},
        {'Conceptfactuur verwijderen': 'Weet u zeker dat u de conceptfactuur wilt verwijderen? Deze actie is niet ongedaan te maken.'},
        {'Bericht printen': 'Let op: de factuur wordt geprint omdat digitaal factureren niet is ingeschakeld. Wilt u doorgaan met printen?'},
    ]);

    // Load screen
    loadScreen($('#default'), {
        url: '/admin/invoice/detailScreen',
        mode: 'read',
        afterLoad: afterLoadScreen
    });

    // Load active sub screen
    $('.kt-widget__item--active').each(function() {
        loadScreen($(this), {
            url: '/admin/invoice/detailScreen',
            afterLoad: afterLoadScreen
        });
    });

    // Switch tab action
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        loadScreen($(e.target), {
            url: '/admin/invoice/detailScreen',
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
    $('body').on('click', '#btnCancelInvoice', function(e) {
        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        setScreenMode(container, 'read');
    });

    // Save action
    $('body').on('click', '#btnSaveInvoice, #btnSaveInvoiceNew', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        save($(this), '/admin/invoice', null, false, null, function(data) {
            if (data.success === true) {
                // When inserted then reload
                if (data.new == true) {
                    window.location = '/admin/invoice/detail/' + data.id;
                }

                loadScreen(container, {
                    url: '/admin/invoice/detailScreen',
                    mode: 'read',
                    afterLoad: afterLoadScreen
                });
            }
        });
    });

    $('body').on('click', '#btnCancelInvoiceNew', function(e) {
        e.preventDefault();

        window.location = '/admin/invoice';
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

        var id = $(this).closest('form').find('input[name=FK_CRM_RELATION]').val();
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

    $('body').on('click', '#insertTimestamp', function(e) {
        e.preventDefault();

        var textarea = $('textarea[name=REMARKS]');

        var currentDate = moment();
        var date = currentDate.format(window.i18n.dateFormat + ' H:mm');

        var currentRemark = textarea.val();
        if (currentRemark != '') {
            currentRemark += '\n';
        }

        currentRemark += date + ' ' + $(this).data('user') + ': ';

        textarea.val(currentRemark);
        textarea.focus();
    });

    // $('body').on('click', '.setFinal', function(e) {
    //     e.preventDefault();
    //
    //     var id = $(this).data('id');
    //
    //     var formData = new FormData();
    //     formData.append('ID', id);
    //
    //     // Confirmation vragen
    //     swal.fire({
    //         text: kjlocalization.get('admin_-_facturen', 'bericht_status_wijziging'),
    //         type: 'warning',
    //         showCancelButton: true,
    //         confirmButtonText: kjlocalization.get('algemeen', 'doorgaan'),
    //         cancelButtonText: kjlocalization.get('algemeen', 'annuleren')
    //     }).then(function(result) {
    //         if (result.value) {
    //             kjrequest('POST', '/admin/invoice/setFinal', formData, true,
    //                 function(data) {
    //                     if (data.success === true)
    //                     {
    //                         swal.fire({
    //                             text: data.message,
    //                             type: "success",
    //                             timer: 1500
    //                         }).then(function(result) {
    //                             loadScreen($('#default'), {
    //                                 url: '/admin/invoice/detailScreen',
    //                                 mode: 'read',
    //                                 afterLoad: afterLoadScreen
    //                             });
    //                         });
    //                     }
    //                     else
    //                     {
    //                         swal.fire({
    //                             text: data.message,
    //                             type: 'error'
    //                         });
    //                     }
    //                 },
    //                 null
    //             );
    //         } else if (result.dismiss === 'cancel') {
    //             return false;
    //         }
    //     });
    // });

    $('body').on('click', '.sendInvoice', function(e) {
        e.preventDefault();

        var id = $(this).data('id');

        var formData = new FormData();
        formData.append('ID', id);

        // Confirmation vragen
        swal.fire({
            text: kjlocalization.get('admin_-_facturen', 'bericht_versturen'),
            type: 'info',
            showCancelButton: true,
            confirmButtonText: kjlocalization.get('algemeen', 'doorgaan'),
            cancelButtonText: kjlocalization.get('algemeen', 'annuleren')
        }).then(function(result) {
            if (result.value) {
                kjrequest('POST', '/admin/invoice/sendInvoice', formData, true,
                    function(data) {
                        processInvoiceSendResult(data);
                    },
                    null
                );
            } else if (result.dismiss === 'cancel') {
                return false;
            }
        });
    });

    $('body').on('click', '.sendInvoiceReminder', function(e) {
        e.preventDefault();

        var id = $(this).data('id');

        var formData = new FormData();
        formData.append('ID', id);

        // Confirmation vragen
        swal.fire({
            text: kjlocalization.get('admin_-_facturen', 'bericht_versturen_herinnering'),
            type: 'info',
            showCancelButton: true,
            confirmButtonText: kjlocalization.get('algemeen', 'doorgaan'),
            cancelButtonText: kjlocalization.get('algemeen', 'annuleren')
        }).then(function(result) {
            if (result.value) {
                kjrequest('POST', '/admin/invoice/sendInvoiceReminder', formData, true,
                    function(data) {
                        processInvoiceSendResult(data);
                    },
                    null
                );
            } else if (result.dismiss === 'cancel') {
                return false;
            }
        });
    });

    $('body').on('click', '.deleteInvoice', function(e) {
        e.preventDefault();

        var id = $(this).data('id');

        // Confirmation vragen
        swal.fire({
            text: kjlocalization.get('admin_-_facturen', 'conceptfactuur_verwijderen'),
            type: 'info',
            showCancelButton: true,
            confirmButtonText: kjlocalization.get('algemeen', 'doorgaan'),
            cancelButtonText: kjlocalization.get('algemeen', 'annuleren')
        }).then(function(result) {
            if (result.value) {
                kjrequest('DELETE', '/admin/invoice/' + id, null, true,
                    function(data) {
                        if (data.success === true)
                        {
                            swal.fire({
                                text: data.message,
                                type: "success",
                                timer: 1500
                            }).then(function(result) {
                                window.location = '/admin/invoice'
                            });
                        }
                        else
                        {
                            swal.fire({
                                text: data.message,
                                type: 'error'
                            });
                        }
                    },
                    null
                );
            } else if (result.dismiss === 'cancel') {
                return false;
            }
        });
    });

    // Line detail
    $('body').on('click', '.selectProduct', function(e) {
        e.preventDefault();

        LastButton = $(this);

        $.ajax({
            url: '/admin/product/modal?selectable=1',
            type: 'GET',
            dataType: 'JSON',

            success: function (data) {
                // Load detail form
                $('.kj_field_modal .modal-title').text(kjlocalization.get('admin_-_dossiers', 'selecteer_product'));
                $('.kj_field_modal .modal-body').html(data.viewDetail);
                loadDatatable($('#ADM_PRODUCT_MODAL_TABLE'));
                loadDropdowns();

                $('.kj_field_modal').modal('show');

                $('.kj_field_modal').off('shown.bs.modal').on('shown.bs.modal', function() {
                    ADM_PRODUCT_MODAL_TABLE_configuration.datatableSelector.redraw();
                });
            }
        });
    });

    $('body').on('click', '.deleteInvoiceLine', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/invoice/line/' + id, null, false,
            function(result) {
                if (result.success) {
                    ADM_INVOICE_LINES_TABLE_configuration.datatableSelector.reload(null, false);

                    loadScreen($('#default'), {
                        url: '/admin/invoice/detailScreen',
                        mode: 'read',
                        afterLoad: afterLoadScreen
                    });
                }
            }
        );
    });
});

function processInvoiceSendResult(data)
{
    if ((data.success === true) && (data.print === true))
    {
        // Confirmation vragen
        swal.fire({
            text: kjlocalization.get('admin_-_facturen', 'bericht_printen'),
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
                                        kjcommunicator.printDocument(
                                            printer,
                                            data.url,
                                            data.fileRequest.TOKEN,
                                            1
                                        );
                                    } else {
                                        swal.fire({
                                            text: kjlocalization.get('erp_facturen', 'printer_niet_ingesteld'),
                                            type: 'error'
                                        }).then(function() {
                                            var fallback_url = '/document/download?token=' + data.fileRequest.TOKEN;
                                            downloadFileAjax('GET', fallback_url);
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
                    var fallback_url = '/document/download?token=' + data.fileRequest.TOKEN;
                    downloadFileAjax('GET', fallback_url);
                }
            } else if (result.dismiss === 'cancel') {
                return false;
            }

            loadScreen($('#default'), {
                url: '/admin/invoice/detailScreen',
                mode: 'read',
                afterLoad: afterLoadScreen
            });
        });
    }
    else if ((data.success === true) && (data.print === false))
    {
        swal.fire({
            text: data.message,
            type: "success",
            timer: 1500
        }).then(function(result) {
            loadScreen($('#default'), {
                url: '/admin/invoice/detailScreen',
                mode: 'read',
                afterLoad: afterLoadScreen
            });
        });
    }
    else
    {
        swal.fire({
            text: data.message,
            type: 'error'
        });
    }
}

function afterLoadScreen(id, screen, data) {
    if (screen === 'lines') {
        loadDatatable($('#ADM_INVOICE_LINES_TABLE'));
    }
    else if (screen === 'documents') {
        loadDropzone();
    }
}

$(document).on('ADM_RELATION_TABLEAfterSelect', function(e, selectedId, linkObj) {
    // Waardes opzoeken
    var name = linkObj.closest('tr').find('[data-field=NAME]').find('span').text();

    var id_input = LastButton.closest('form').find('input[name=FK_CRM_RELATION]');
    id_input.val(selectedId);

    var text_input = LastButton.closest('div.form-group').find('input[type=text]');
    text_input.val(name);

    // Contactpersonen refreshen
    $.ajax({
        url: '/admin/crm/relation/contact/allByRelation/' + selectedId,
        type: 'GET',
        dataType: 'JSON',

        success: function (data) {
            var select =  $('#FK_CRM_CONTACT');

            select.empty();
            $.each(data.items, function (index, value) {
                select.append($("<option></option>").attr("value", index).text(value));
            });

            select.val('');
            select.selectpicker('refresh');
        }
    });

    // Adressen refreshen
    $.ajax({
        url: '/admin/crm/address/allByRelation/' + selectedId,
        type: 'GET',
        dataType: 'JSON',

        success: function (data) {
            var select =  $('#FK_CRM_RELATION_ADDRESS');

            select.empty();
            $.each(data.items, function (index, value) {
                select.append($("<option></option>").attr("value", index).text(value));
            });

            select.val('');
            select.selectpicker('refresh');
        }
    });

    // Modal hidden
    $('.kj_field_modal').modal('hide');
});

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

$(document).on('ADM_INVOICE_LINES_TABLEAfterLoad', function(e, detailDiv) {
    detailDiv.find('.kj_save').off('click').on('click', function(e) {
        e.preventDefault();

        save($(this), ADM_INVOICE_LINES_TABLE_configuration.saveUrl, ADM_INVOICE_LINES_TABLE_configuration.parentid, (ADM_INVOICE_LINES_TABLE_configuration.inlineEdit === true), detailDiv, function(data) {
            if (data.success === true) {
                ADM_INVOICE_LINES_TABLE_configuration.datatableSelector.reload(null, false);

                loadScreen($('#default'), {
                    url: '/admin/invoice/detailScreen',
                    mode: 'read',
                    afterLoad: afterLoadScreen
                });
            }
        });

        return false;
    });
});