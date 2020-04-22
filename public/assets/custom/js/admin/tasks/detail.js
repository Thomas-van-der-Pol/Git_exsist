$(document).ready(function() {
    // Load screen
    loadScreen($('#default'), {
        url: '/admin/tasks/detailScreen',
        mode: 'edit',
        afterLoad: afterLoadScreen
    });

    // Cancel action
    $('body').on('click', '#btnCancelTask', function(e) {
        e.preventDefault();

        window.location = '/admin/tasks';
    });

    $('body').on('click', '#btnSaveTask', function(e) {
        e.preventDefault();

        save($(this), '/admin/tasks', null, false, null, function(data) {
            // When inserted then reload
            loadScreen($('#default'), {
                url: '/admin/tasks/detailScreen',
                mode: 'edit',
                afterLoad: afterLoadScreen
            });
        });
    });

    $('body').on('click', '.activateItem', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/tasks/' + id, null, false,
            function(result) {
                if (result.success) {
                    loadScreen($('#default'), {
                        url: '/admin/tasks/detailScreen',
                        mode: 'edit',
                        afterLoad: afterLoadScreen
                    });
                }
            }
        );
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

    $('body').on('click', '.openProject', function(e) {
        e.preventDefault();

        var id = $(this).closest('form').find('input[name=FK_PROJECT]').val();
        if (id > 0) {
            // Open client in new window
            var win = window.open('/admin/project/detail/' + id, '_blank');
            if (win) {
                // Browser has allowed it to be opened
                win.focus();
            } else {
                // Browser has blocked it
            }

            return false;
        }
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

    $('body').on('click', '.openProduct', function(e) {
        e.preventDefault();

        var id = $(this).closest('.md-form').next('input[type="hidden"]').val();
        if (id > 0) {
            // Open client in new window
            var win = window.open('/admin/product/detail/' + id, '_blank');
            if (win) {
                // Browser has allowed it to be opened
                win.focus();
            } else {
                // Browser has blocked it
            }

            return false;
        }
    });
});

function afterLoadScreen(id, screen, data) {
    if (screen === 'default') {
        var input = document.getElementById('CATEGORIES');
        var wl =  $(input).data('wl');
        // Tagify voor filter velden
        var tagify = new Tagify(input, {
            whitelist: Object.values(wl),
            dropdown: {
                enabled: 1
            }
        });

        tagify.on('add', function(e, tagName){
            $(input).change();
        });
        tagify.on('remove', function(e, tagName){
            $(input).change();
        });

        tagify.DOM.scope.parentNode.insertBefore(tagify.DOM.input, tagify.DOM.scope);
        $('.kjtagify').next().addClass('active');
    }
}
$(document).on('ADM_PRODUCT_MODAL_TABLEAfterSelect', function(e, selectedId, linkObj) {
    // Waardes opzoeken
    var name = linkObj.closest('tr').find('[data-field=DESCRIPTION_INT]').find('span').text();

    var id_input = LastButton.closest('form').find('input[name=FK_ASSORTMENT_PRODUCT]');
    id_input.val(selectedId);

    var text_input = LastButton.closest('div.form-group').find('input[type=text]');
    text_input.val(name);

    setMaterialActiveLabels(LastButton.closest('div.kt-portlet__body'));
    // Modal hidden
    $('.kj_field_modal').modal('hide');
});
