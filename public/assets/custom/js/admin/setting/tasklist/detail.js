$(document).ready(function() {
    // Load screen
    loadScreen($('#default'), {
        url: '/admin/settings/tasklist/detailScreen',
        mode: 'read',
        afterLoad: afterLoadScreen
    });

    // Load active sub screen
    $('.kt-widget__item--active').each(function() {
        loadScreen($(this), {
            url: '/admin/settings/tasklist/detailScreen',
            afterLoad: afterLoadScreen
        });
    });

    // Switch tab action
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        loadScreen($(e.target), {
            url: '/admin/settings/tasklist/detailScreen',
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
    $('body').on('click', '#btnCancelTasklist', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        setScreenMode(container, 'read');
    });

    // Save action
    $('body').on('click', '#btnSaveTasklist, #btnSaveTasklistNew', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');
        if (!container.length) {
            container = $(this).closest('.kt-widget');
        }

        save($(this), '/admin/settings/tasklist', null, false, null, function(data) {
            // When inserted then reload
            if (data.new == true) {
                window.location = '/admin/settings/tasklist/detail/' + data.id;
            }

            loadScreen(container, {
                url: '/admin/settings/tasklits/detailScreen',
                mode: 'read',
                afterLoad: afterLoadScreen
            });
        });
    });

    $('body').on('click', '#btnCancelTasklistNew', function(e) {
        e.preventDefault();

        window.location = '/admin/settings/tasklist';
    });

    $('body').on('click', '.activateItem', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/settings/tasklist/' + id, null, false,
            function(result) {
                if (result.success) {
                    loadScreen($('#default'), {
                        url: '/admin/settings/tasklist/detailScreen',
                        mode: 'read',
                        afterLoad: afterLoadScreen
                    });
                }
            }
        );
    });
});

function afterLoadScreen(id, screen, data) {
    if (screen === 'tasks') {
        loadTaskScreen($('#'+screen));
    }
}