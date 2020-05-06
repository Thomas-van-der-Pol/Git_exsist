var loadedType = null;

$(document).ready(function() {

    // Load active sub screen
    $('.kt-widget__item--active').each(function() {
        var screen = $(this).attr('href').replace('#', '');
        var type = $('#'+screen).data('type');

        loadScreen($(this), {
            url: '/admin/project/detailScreenOverview',
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

        storeSession('ADM_PROJECT', 'CURRENT_TAB', type);

        loadScreen($(e.target), {
            url: '/admin/project/detailScreenOverview',
            afterLoad: afterLoadScreen,
            postData: [
                {'type': type},
            ]
        });
    });
});

function afterLoadScreen(id, screen, data) {
    loadedType = data.type;

    // Screen switch tab
    $('#' + screen + ' a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        loadFilteredDatatable($(e.target), true);
    });

    var activeTab = $('#' + screen).find('.nav-link.active');
    loadFilteredDatatable(activeTab, false);
}

function loadFilteredDatatable(target, doRender)
{
    var state = target.data('state'); // activated tab
    var configName = 'ADM_PROJECT_TABLE_' + loadedType + '_' + state;
    var configuration = window[configName + '_configuration'];

    if (configuration.datatableSelector === undefined) {
        // Add filters
        configuration.source.params = {
            ADM_PROJECT_FILTER_SEARCH: $('#ADM_PROJECT_FILTER_SEARCH').val() || '',
            ACTIVE: $('#ADM_FILTER_PROJECT_STATUS').val() || '',
            SHOW_DONE: $('#SHOW_DONE:checkbox:checked').length.toString() || '0'
        };

        // Remove empty filters from arrays
        $.each(configuration.source.params, function (k, v) {
            if (v === '' || $.isEmptyObject(v)) {
                delete configuration.source.params[k];
            }
        });

        // Load datatable
        loadDatatable($('#'+configName));
    } else {
        // Retrieve query
        var query = configuration.datatableSelector.getDataSourceQuery();

        // Set filters manually
        query['ADM_PROJECT_FILTER_SEARCH'] = $('#ADM_PROJECT_FILTER_SEARCH').val() || '';
        query['ACTIVE'] = $('#ADM_FILTER_PROJECT_STATUS').val() || '';
        query['SHOW_DONE'] = $('#SHOW_DONE:checkbox:checked').length.toString() || '0';

        // remove empty element from arrays
        $.each(query, function (k, v) {
            if (v === '' || $.isEmptyObject(v)) {
                delete query[k];
            }
        });

        // Set filters on datatable
        configuration.datatableSelector.setDataSourceQuery(query);

        // Reload datatable
        // if (doRender === true) {
            configuration.datatableSelector.dataRender();
        // }
    }
}