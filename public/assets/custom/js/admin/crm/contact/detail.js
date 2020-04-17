function initAnonymizeFunction(id) {
    var formData = new FormData();
    formData.append('id', id);

    swal.fire({
        title: kjlocalization.get('algemeen', 'anonimiseren_titel'),
        text: kjlocalization.get('algemeen', 'anonimiseren_text'),
        type: 'warning',
        width: 600,
        showCancelButton: true,
        confirmButtonText: kjlocalization.get('algemeen', 'doorgaan'),
        cancelButtonText: kjlocalization.get('algemeen', 'annuleren')
    }).then(function(result) {
        if (result.value) {
            kjrequest('POST', '/admin/crm/relation/contact/anonimyze', formData, true, function (result) {
                if (result.success) {
                    $.notify({message: result.message}, {type: 'success'});

                    ADM_RELATION_CONTACT_TABLE_configuration.datatableSelector.reload(null, false);
                }
            });
        }
    });
}