$(document).ready(function() {
    kjlocalization.create('Algemeen', [
        {'Actief': 'Actief'},
        {'Inactief': 'Inactief'},
        {'Doorgaan': 'Doorgaan'},
        {'Annuleren': 'Annuleren'},
        {'Inklappen': 'Inklappen'},
        {'Uitklappen': 'Uitklappen'},
        {'Verwijderen': 'Verwijderen'},
        {'Kopieren': 'Kopieren'},
        {'Openen': 'Openen'},
        {'Ja': 'Ja'},
        {'Nee': 'Nee'},
        {'Anonimiseren titel': 'Weet je het zeker?'},
        {'Anonimiseren text': 'Weet je zeker dat je door wilt gaan?'},
        {'Selecteer minimaal een regel': 'Selecteer minimaal een regel om door te gaan'}
    ]);
});

$(document).on('kj_managebuttonOnLoad', function(e, modalBody, dropdownTypeID) {
    $.ajax({
        url: '/admin/dropdownvalue?typeid=' + dropdownTypeID,
        type: 'GET',
        dataType: 'JSON',

        success: function (data) {
            // Load detail form
            modalBody.html(data.viewDetail);
            loadDropdowns();
            loadDatatable($('#ADM_DROPDOWNVALUE_TABLE'));

            $('.kj_field_modal').off('shown.bs.modal').on('shown.bs.modal', function() {
                ADM_DROPDOWNVALUE_TABLE_configuration.datatableSelector.redraw();
            });
        }
    });
});

$(document).on('kjfieldmodal_onHidden', function(e, modal,dropdownTypeID) {
    //Check of je dropdown kan vinden
    var targetDD = $('.kt-bootstrap-select[data-id='+dropdownTypeID+']');
    if(targetDD.length > 0) {
        $.ajax({
            url: '/admin/dropdownvalue/allByTypeRendered/' + dropdownTypeID,
            type: 'GET',
            dataType: 'JSON',

            success: function (data) {
                var ddEl =  $('.kt-bootstrap-select[data-id='+dropdownTypeID+']');
                var rememberVal = $('.kt-bootstrap-select[data-id='+dropdownTypeID+'] option:selected').val();

                ddEl.empty();
                $.each(data.results, function (key, value) {
                    ddEl.append($("<option></option>").attr("value", key).text(value));
                });

                ddEl.val(rememberVal);
                ddEl.selectpicker('refresh');
            }
        });
    }
});

$(document).on('communicatorDownload', function(e) {
    e.preventDefault();
    downloadFileAjax('GET', '/admin/communicator/download');
});