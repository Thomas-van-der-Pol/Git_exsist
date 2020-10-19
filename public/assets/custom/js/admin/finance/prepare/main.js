$(document).ready(function() {

    $('input[name="REFERENCE_DATE"]').trigger('change');

    $('body').on('click', '#btnUpdateData', function(e) {
        e.preventDefault();

        if($('input[name=REFERENCE_DATE]').val().length === 0){
            swal.fire({
                text: kjlocalization.get('admin_-_facturen', 'peildatum_niet_gevuld'),
                type: 'error'
            });

            return;
        }

        startKJLoader({funVersion: true, CrazyLoader: false});

        $('input[name="REFERENCE_DATE"]').trigger('change');

        var formData = new FormData();
        formData.append('DATE', $('input[name=REFERENCE_DATE]').val());

        kjrequest('POST','/admin/invoice/prepare/process', formData, true,
            function() {
                stopKJLoader();
                ADM_BILLCHECK_TABLE_configuration.selected = [];
                ADM_BILLCHECK_TABLE_configuration.datatableSelector.reload(null, false);
                showCheckedRows('ADM_BILLCHECK_TABLE');
            },
            function() {
                stopKJLoader();
            }
        );
    });

    $('body').on('click', '#createInvoices', function(e) {
        e.preventDefault();

        var ids = getCheckedRows('ADM_BILLCHECK_TABLE');
        if(ids.length === 0){
            swal.fire({
                text: kjlocalization.get('admin_-_facturen', 'geen_facturen_geselecteerd'),
                type: 'error'
            });

            return;
        }

        startKJLoader({funVersion: true, CrazyLoader: false});

        var splitids = new Array();
        for (i = 0; i < ids.length; i++) {
            var split = ids[i].split(',');
            for (ii = 0; ii < split.length; ii++) {
                splitids.push(split[ii]);
            }
        }

        var formData = new FormData();
        formData.append('DATE', $('input[name=REFERENCE_DATE]').val());
        formData.append('IDS', JSON.stringify(splitids));

        kjrequest('POST','/admin/invoice/prepare/createInvoices', formData, true,
            function(data) {
                stopKJLoader();

                if (data.success === true) {
                    ADM_BILLCHECK_TABLE_configuration.selected = [];
                    ADM_BILLCHECK_TABLE_configuration.datatableSelector.reload(null, false);
                    showCheckedRows('ADM_BILLCHECK_TABLE');
                }
            },
            function() {
                stopKJLoader();
            }
        );
    });
});

$(document).on('ADM_BILLCHECK_TABLERowCallback', function(e, row, data, index) {
    if (data.INVALID == true || data.INVALID_ADDRESS == true) {
        row.addClass('font-danger');
        row.addClass('block-selection');
        row.find('td[data-field="BILLCHECKIDString"]').replaceWith('<td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-autohide-disabled="false"><span></span></td>');
    }
});