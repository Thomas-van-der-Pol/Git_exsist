var loadedType = null;

$(document).ready(function() {

    // Translations
    kjlocalization.create('Admin - Boekhouding', [
        {'Successvol geexporteerd': 'De gegevens zijn succesvol geëxporteerd!'},
        {'Exporteren mislukt': 'Fout bij exporteren van gegevens'},
        {'Successvol geimporteerd': 'De gegevens zijn succesvol geïmporteerd!'},
        {'Importeren mislukt': 'Fout bij importeren van gegevens'}
    ]);

    // Load active sub screen
    $('.kt-widget__item--active').each(function() {
        var screen = $(this).attr('href').replace('#', '');
        var type = $('#'+screen).data('type');

        loadScreen($(this), {
            url: '/admin/accountancy/detailScreenOverview',
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

        storeSession('ADM_ACCOUNTANCY', 'CURRENT_TAB', type);

        loadScreen($(e.target), {
            url: '/admin/accountancy/detailScreenOverview',
            afterLoad: afterLoadScreen,
            postData: [
                {'type': type},
            ]
        });
    });


    $('body').on('click', '#export', function(e) {
        e.preventDefault();

        var datatable_name = null;
        var url = null;

        if (loadedType == 1) {
            datatable_name = 'ADM_ACCOUNTANCY_DEBTOR_TABLE';
            url = '/admin/accountancy/debtor/export';
        }
        else if (loadedType == 2) {
            datatable_name = 'ADM_ACCOUNTANCY_CREDITOR_TABLE';
            url = '/admin/accountancy/creditor/export';
        }
        else if (loadedType == 3) {
            datatable_name = 'ADM_ACCOUNTANCY_INVOICE_TABLE';
            url = '/admin/accountancy/invoice/export';
        }

        var configuration = window[datatable_name + '_configuration'];

        var ids = getCheckedRows(datatable_name);

        if (ids.length === 0) {
            swal.fire({
                text: kjlocalization.get('algemeen', 'selecteer_minimaal_een_regel'),
                type: 'error'
            });

            return false;
        }

        var formData = new FormData();
        formData.append('ids', JSON.stringify(ids));

        startKJLoader({funVersion:true,CrazyLoader:true});

        kjrequest('POST', url, formData, true,
            function(data) {
                stopKJLoader();

                configuration.selected = [];
                configuration.datatableSelector.reload(null, false);
                showCheckedRows(datatable_name);

                swal.fire({
                    text: kjlocalization.get('admin_-_boekhouding', 'successvol_geexporteerd'),
                    type: 'info'
                });
            },
            function(data) {
                stopKJLoader();

                configuration.datatableSelector.reload(null, false);

                swal.fire({
                    text: kjlocalization.get('admin_-_boekhouding', 'exporteren_mislukt') + ': ' + data.message,
                    type: 'error'
                });
            }
        );
    });

    $('body').on('click', '#importReceivables', function(e) {
        e.preventDefault();

        startKJLoader({funVersion:true,CrazyLoader:true});

        kjrequest('POST', '/admin/accountancy/receivable/import', null, true,
            function(data) {
                stopKJLoader();

                swal.fire({
                    text: kjlocalization.get('admin_-_boekhouding', 'successvol_geimporteerd'),
                    type: 'info'
                });
            },
            function(data) {
                stopKJLoader();

                swal.fire({
                    text: kjlocalization.get('admin_-_boekhouding', 'importeren_mislukt') + ': ' + (data.message || ''),
                    type: 'error'
                });
            }
        );
    });
});

function afterLoadScreen(id, screen, data) {
    loadedType = data.type;

    // Set visible buttons based on type
    if (screen === 'receivables') {
        $('#export').hide();
    } else {
        $('#export').show();
    }

    // Load tables
    if (screen === 'debtors') {
        loadDatatable($('#ADM_ACCOUNTANCY_DEBTOR_TABLE'));
    }
    else if (screen === 'creditors') {
        loadDatatable($('#ADM_ACCOUNTANCY_CREDITOR_TABLE'));
    }
    else if (screen === 'invoices') {
        loadDatatable($('#ADM_ACCOUNTANCY_INVOICE_TABLE'));
    }
}