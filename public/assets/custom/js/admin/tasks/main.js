$(document).ready(function() {
    kjlocalization.create('Admin - Taken', [
        {'Map verwijderen titel': 'Weet je het zeker'},
        {'Map verwijderen tekst': 'Alle taken in deze map worden ontkoppeld van deze map.'}
    ]);
    // Load active tab
    loadTaskItemActive();

    //load daterange picker
    loadDateRangePickers();

    // Switch tab action
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        // Page reset to first page
        taskPage = 1;

        // Remember current tab
        var screen = $(e.target).attr('href').replace('#', '');
        var type = $('#'+screen).data('type');
        storeSession('ADM_TASK', 'CURRENT_TAB', type);

        // Load screen
        loadTaskScreen($(e.target));
    });

    $('input[name="ADM_FILTER_TASK_DATE"]').on('apply.daterangepicker', function(ev, picker) {
        beginDate = picker.startDate.format('DD-MM-YYYY');
        endDate =  picker.endDate.format('DD-MM-YYYY');
        loadTaskItemActive(beginDate, endDate);
    });

    $('input[name="ADM_FILTER_TASK_DATE"]').on('cancel.daterangepicker', function(ev, picker) {
        loadTaskItemActive();
    });

    // Change assignee filter
    $('body').on('change', 'select[name=ADM_FILTER_TASK_ASSIGNEE], select[name=ADM_FILTER_TASK_FILTERS]', function(e) {
        e.preventDefault();
        loadTaskItemActive();
    });
});

function loadTaskItemActive(beginDate = null, endDate = null) {
    // Page reset to first page
    taskPage = 1;

    if(beginDate == null && endDate == null){
        // Load active sub screen
        $('.kt-widget__item--active').each(function() {
            loadTaskScreen($(this));
        });
    }
    else{
        $('.kt-widget__item--active').each(function() {
            loadTaskScreen($(this),beginDate,endDate);
        });
    }
}

$('.editCustomMap').on('click', function () {
    var id = $(this).data('id');
    LoadCustomMapModal(id);
});

$('.deleteCustomMap').on('click', function () {
    var id = $(this).data('id');
    // Wel bereikbaar, maar verkeerde versie
    swal.fire({
        title: kjlocalization.get('admin_-_taken', 'map_verwijderen_titel'),
        text: kjlocalization.get('admin_-_taken', 'map_verwijderen_tekst'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: kjlocalization.get('algemeen', 'doorgaan'),
        cancelButtonText: kjlocalization.get('algemeen', 'annuleren')
    }).then(function(result) {
        if (result.value) {
            kjrequest('DELETE', '/admin/tasks/custommap/' + id, null, true, function (data) {
                if(data.success){
                    location.reload();
                }
            })
        }
    });
});