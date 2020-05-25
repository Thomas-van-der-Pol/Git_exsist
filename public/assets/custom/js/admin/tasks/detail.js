$(document).ready(function() {
    // Load screen
    loadScreen($('#default'), {
        url: '/admin/tasks/detailScreen',
        mode: 'edit',
        afterLoad: afterLoadScreen
    });

    // Edit action
    $('body').on('click', '.setEditMode', function(e) {
        e.preventDefault();

        var target = $(this).data('target');
        setScreenMode($('#' + target), 'edit');
    });

    // Cancel action
    $('body').on('click', '#btnCancelTask', function(e) {
        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        setScreenMode(container, 'read');
    });

    $('body').on('click', '#btnSaveTask', function(e) {
        e.preventDefault();

        save($(this), '/admin/tasks', null, false, null, function(data) {
            if (data.success === true) {
                // When inserted then reload
                loadScreen($('#default'), {
                    url: '/admin/tasks/detailScreen',
                    mode: 'edit',
                    afterLoad: afterLoadScreen
                });
            }
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