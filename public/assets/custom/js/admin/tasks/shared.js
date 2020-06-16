var taskPage = 1;

$(document).ready(function() {

    // Translations
    kjlocalization.create('Admin - Taken', [
        {'Nieuwe taak': 'Nieuwe taak'}
    ]);

    // New task
    $('body').on('click', '.newTask', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        var type = $(this).data('type');
        var pid = $(this).data('pid');
        var container = $(this).closest('.tab-pane');

        loadTaskModal(id, type, pid, null, container);
    });

    // New tasklist
    $('body').on('click', '.newStandardTaskList', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        var type = $(this).data('type');
        var pid = $(this).data('pid');
        var container = $(this).closest('.tab-pane');

        loadTaskListModal(id, type, pid, null, container);
    });

    // Group selection
    $('body').on('click', '.kt-todo__toolbar .kt-todo__check .kt-checkbox input', function() {
        var listEl = KTUtil.getByID('kt-todo__list');
        var items = KTUtil.findAll(listEl, '.kt-todo__items .kt-todo__item');

        for (var i = 0, j = items.length; i < j; i++) {
            var item = items[i];
            var checkbox = KTUtil.find(item, '.kt-todo__actions .kt-checkbox input');
            checkbox.checked = this.checked;

            if (this.checked) {
                KTUtil.addClass(item, 'kt-todo__item--selected');
            } else {
                KTUtil.removeClass(item, 'kt-todo__item--selected');
            }
        }
    });

    $('body').on('click', '.setDone', function(e) {
        e.preventDefault();

        var status = $(this).data('status');
        var container = $(this).closest('.tab-pane');
        var taskIds = $.map($('input[name="DONE"]:checked'), function(c){
            return c.id;
        });

        if (taskIds.length === 0) {
            swal.fire({
                text: kjlocalization.get('algemeen', 'selecteer_minimaal_een_regel'),
                type: 'error'
            });

            return false;
        }

        var formData = new FormData();
        formData.append('status', status);
        formData.append('task', JSON.stringify(taskIds));

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/admin/tasks/setDone',
            type: 'POST',
            contentType: false,
            processData: false,
            data: formData,

            success: function (data) {
                if (data.success === true) {
                    loadTaskScreen(container);
                }
            }
        });
    });



    $('body').on('click', '.kt-todo__details', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        var type = $(this).data('type');
        window.location = '/admin/tasks/detail/' + id + '?type=' + type;
    });

    $('body').on('click', '.kt-star', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        var subscriptionId = $(this).data('subscription');
        var container = $(this).closest('.tab-pane');

        var formData = new FormData();
        formData.append('id', id);
        formData.append('subscriptionId', subscriptionId);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/admin/tasks/setSubscription',
            type: 'POST',
            contentType: false,
            processData: false,
            data: formData,

            success: function (data) {
                if (data.success === true) {
                    loadTaskScreen(container);
                }
            }
        });
    });

    $('body').on('click', '.prevTaskPage', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');

        if (taskPage > 1) {
            taskPage = taskPage - 1;
            loadTaskScreen(container);
        }
    });

    $('body').on('click', '.nextTaskPage', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');

        taskPage = taskPage + 1;
        loadTaskScreen(container);
    });

    //Enter zoeken
    $('body').on('keypress', '#ADM_TASK_FILTER_SEARCH', function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code === 13) {
            // Store session
            var value = $(this).val();
            storeSession('ADM_TASK', 'ADM_TASK_FILTER_SEARCH', value);

            // Load items
            var container = $(this).closest('.tab-pane');
            loadTaskScreen(container);
        }
    });


    $('body').on('change', '#ADM_TASK_FILTER_ACTIVE', function() {
        // Store session
        var value = $(this).val();
        storeSession('ADM_TASK', 'ADM_TASK_FILTER_ACTIVE', value);

        // Load items
        var container = $(this).closest('.tab-pane');
        loadTaskScreen(container);

    });

    $('body').on('change', '#ADM_TASK_FILTER_STATUS', function() {
        // Store session
        var value = $(this).val();
        storeSession('ADM_TASK', 'ADM_TASK_FILTER_STATUS', value);

        // Load items
        var container = $(this).closest('.tab-pane');
        loadTaskScreen(container);

    });

    $('body').on('change', '#ADM_FILTER_TASK_FILTERS', function() {
        // Store session
        var value = $(this).val();
        storeSession('ADM_TASK', 'ADM_FILTER_TASK_FILTERS', value);

        // Load items
        var container = $(this).closest('.tab-pane');
        loadTaskScreen(container);

    });
    $('body').on('click', '.shiftDeadline, .connectEmployee, .copyToMap', function(e) {
        e.preventDefault();

        var container = $(this).closest('.tab-pane');
        var taskIds = $.map($('input[name="DONE"]:checked'), function(c){
            return c.id;
        });

        if (taskIds.length === 0) {
            swal.fire({
                text: kjlocalization.get('algemeen', 'selecteer_minimaal_een_regel'),
                type: 'error'
            });

            return false;
        }

        var type = $(this).data('type');
        var subject = null;
        if(type === 'shiftDeadline'){
            subject = kjlocalization.get('admin_-_taken', 'deadline_verschuiven');
        }
        else if(type === 'connectEmployee'){
            subject = kjlocalization.get('admin_-_taken', 'koppelen_aan_medewerker');
        }
        else{
            subject = kjlocalization.get('admin_-_taken', 'toevoegen_aan_map');
        }

        loadTaskFunctionModal(type, taskIds, subject, container);
    });

    $('body').on('click', '#addMap', function(e) {
        e.preventDefault();
        LoadCustomMapModal(-1);
    });
});

function loadTaskFunctionModal(type, taskIds, subject = null, container) {
    var url = '/admin/tasks/functions/modal/?type=' + type;
    if (type) {
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'JSON',

            success: function (data) {
                // Load detail form
                $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').text(kjlocalization.get('algemeen', 'opslaan'));
                $('.kj_field_modal .modal-title').text(subject || kjlocalization.get('admin_-_taken', 'taken'));

                $('.kj_field_modal .modal-lg').css('max-width', '500px');
                $('.kj_field_modal .modal-body').html(data.viewDetail);

                setMaterialActiveLabels($('.kj_field_modal .modal-body'));

                $('.kj_field_modal').modal('show');
                loadDatePickers();
                loadDropdowns();

                $('.kj_field_modal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
                    $('.kj_field_modal .modal-lg').css('max-width', '');
                });

                // Callback voor sluiten modal
                $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').off().on('click', function (e) {
                    e.preventDefault();
                    url = '/admin/tasks/' + type;
                    var form = $('.kj_field_modal .modal-body').find('#detailFormFunctionModal');

                    if (!form.valid()) {
                        return false;
                    }

                    var rawData = form.serializeArray();
                    var formData = new FormData();

                    //Alle velden
                    rawData.forEach(function (data, index, form) {
                        formData.append(data.name, data.value);
                    });
                    formData.append('task', JSON.stringify(taskIds));
                    kjrequest('POST', url, formData, false, function (data) {
                        if (data.success === true) {
                            loadTaskScreen(container);
                        }
                    });
                });

                $('.kj_field_modal').find('.modal-body').find('form').find('.kt-portlet__body').find('input').keypress(function (e) {
                    var key = e.which;
                    if (key == 13) {
                        e.preventDefault();// the enter key code
                        $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').click();
                    }
                });
            }
        });
    }
}

function loadTaskScreen(object, beginDate = null, endDate = null)
{
    // Clear inactive tab-panes
    $('.tab-pane:not(.active)').empty();

    // Get options
    var url = '/admin/tasks/retrieveTasks';

    // Handle active tab
    var nav = object.closest('.nav');
    if (nav.length) {
        object.closest('.nav').find('a[data-toggle="tab"]').removeClass('kt-widget__item--active').removeClass('active');
        object.addClass('kt-widget__item--active');
    }

    // Get variables
    var screen = '';
    if (object.is('a')) {
        screen = object.attr('href').replace('#', '');
    } else {
        screen = object.attr('id');
    }

    var screenEl = $('#'+screen);
    var type = (screenEl.data('type') || 0);
    var pid = (screenEl.data('id') || 0);
    var page = taskPage;

    var container = screenEl.closest('.kt-portlet');

    KTApp.block(container);

    // Determine assignee and hide/show filter if necessary
    var assignee = 0;
    if ($('#ADM_FILTER_TASK_ASSIGNEE').length) {
        assignee = $('#ADM_FILTER_TASK_ASSIGNEE').val();

        if (screen === 'subscribed_tasks') {
            $('#ASSIGNEE_FILTER').hide();
        } else {
            $('#ASSIGNEE_FILTER').show();
        }
    }

    // Hide/show deadline filter if necessary
    if ($('.filterDeadline').length) {
        if ((screen === 'today_tasks') || (screen === 'this_week_tasks') || (screen === 'this_month_tasks')) {
            $('.filterDeadline').hide();
        } else {
            $('.filterDeadline').show();
        }
    }

    var category = $('#ADM_FILTER_TASK_FILTERS').val()|| '';

    if(beginDate == null && endDate == null){
        beginDate = $('#ADM_FILTER_TASK_DATE').data('start-date') || '';
        endDate = $('#ADM_FILTER_TASK_DATE').data('end-date') || '';
    }

    var filter = ($('#ADM_TASK_FILTER_SEARCH').val() || '');
    var active = ($('#ADM_TASK_FILTER_ACTIVE').val() || '');
    var status = ($('#ADM_TASK_FILTER_STATUS').val() || '');

    var formData = new FormData();
    formData.append('SCREEN', screen);
    formData.append('TYPE', type);
    formData.append('PID', pid);
    formData.append('ASSIGNEE', assignee);
    formData.append('CATEGORY', category);
    formData.append('FILTER', filter);
    formData.append('ACTIVE', active);
    formData.append('STATUS', status);
    formData.append('PAGE', page);
    formData.append('BEGINDATE', beginDate);
    formData.append('ENDDATE', endDate);

    // Load data
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,

        success: function(data) {
            if (data.success === true) {
                screenEl.html(data.view);
                KTApp.initTooltips();
                loadDropdowns();
                screenEl.find('#ADM_TASK_FILTER_SEARCH').focus();
                KTApp.unblock(container);
            }
        }
    });
}

function loadTaskModal(id, type, pid, subject = null, container)
{
    var url = '/admin/tasks/modal/' + id + '?type=' + type;
    if (pid > 0) {
        url += '&pid=' + pid;
    }

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'JSON',

        success: function (data) {
            // Load detail form
            $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').text(kjlocalization.get('algemeen', 'opslaan'));
            $('.kj_field_modal .modal-title').text(subject || kjlocalization.get('admin_-_taken', 'nieuwe_taak'));

            $('.kj_field_modal .modal-lg').css('max-width', '500px');
            $('.kj_field_modal .modal-body').html(data.viewDetail);

            setMaterialActiveLabels($('.kj_field_modal .modal-body'));

            $('.kj_field_modal').modal('show');
            loadDatePickers();
            loadDropdowns();

            $('.kj_field_modal').off('shown.bs.modal').on('shown.bs.modal', function() {
                if (id == -1) {
                    $('input[name=SUBJECT]').focus();
                }
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
            });

            $('.kj_field_modal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
                $('.kj_field_modal .modal-lg').css('max-width', '');
            });

            $('body').on('keypress', 'input[name=SUBJECT]', function (e) {
                if (e.which == 13) {
                    e.preventDefault();

                    $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').click();
                }
            });

            // Callback voor sluiten modal
            $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').off().on('click', function (e) {
                e.preventDefault();

                var form = $('.kj_field_modal .modal-body').find('#detailFormTasks');

                if (!form.valid()) {
                    return false;
                }

                var rawData = form.serializeArray();
                var formData = new FormData();

                //Alle velden
                rawData.forEach(function(data, index, form) {
                    formData.append(data.name, data.value);
                });

                // Alle checkboxes unchecked als value false
                form.find('input[type=checkbox]:unchecked').each(function(index, data) {
                    formData.append(data.name, 0);
                });

                kjrequest('POST', '/admin/tasks', formData, true, function (result) {
                    if (result.success) {
                        loadTaskScreen(container);
                    }
                });
            });
        }
    });
}

function loadTaskListModal(id, type, pid, subject = null, container)
{
    var url = '/admin/tasks/list/modal/' + id + '?type=' + type;
    if (pid > 0) {
        url += '&pid=' + pid;
    }

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'JSON',

        success: function (data) {
            // Load detail form
            $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').text(kjlocalization.get('algemeen', 'opslaan'));
            $('.kj_field_modal .modal-title').text(subject || kjlocalization.get('admin_-_taken', 'takenlijst_toevoegen'));

            $('.kj_field_modal .modal-lg').css('max-width', '500px');
            $('.kj_field_modal .modal-body').html(data.viewDetail);

            setMaterialActiveLabels($('.kj_field_modal .modal-body'));

            $('.kj_field_modal').modal('show');
            loadDatePickers();
            loadDropdowns();

            $('.kj_field_modal').off('shown.bs.modal').on('shown.bs.modal', function() {
                if (id == -1) {
                    $('input[name=SUBJECT]').focus();
                }
            });

            $('.kj_field_modal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
                $('.kj_field_modal .modal-lg').css('max-width', '');
            });

            $('body').on('keypress', 'input[name=SUBJECT]', function (e) {
                if (e.which == 13) {
                    e.preventDefault();

                    $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').click();
                }
            });

            // Callback voor sluiten modal
            $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').off().on('click', function (e) {
                e.preventDefault();

                var form = $('.kj_field_modal .modal-body').find('#detailFormTasks');

                if (!form.valid()) {
                    return false;
                }

                var rawData = form.serializeArray();
                var formData = new FormData();

                //Alle velden
                rawData.forEach(function(data, index, form) {
                    formData.append(data.name, data.value);
                });

                // Alle checkboxes unchecked als value false
                form.find('input[type=checkbox]:unchecked').each(function(index, data) {
                    formData.append(data.name, 0);
                });

                kjrequest('POST', '/admin/tasks/list', formData, true, function (result) {
                    if (result.success) {
                        loadTaskScreen(container);
                    }
                });
            });
        }
    });
}

function LoadCustomMapModal(id)
{
    var url = '/admin/tasks/custommap/modal/' + id;

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'JSON',

        success: function (data) {
            // Load detail form
            $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').text(kjlocalization.get('algemeen', 'opslaan'));
            $('.kj_field_modal .modal-title').text(id === -1? kjlocalization.get('admin_-_taken', 'map_aanmaken'): kjlocalization.get('admin_-_taken', 'map_aanpassen') );

            $('.kj_field_modal .modal-lg').css('max-width', '500px');
            $('.kj_field_modal .modal-body').html(data.viewDetail);

            setMaterialActiveLabels($('.kj_field_modal .modal-body'));

            $('.kj_field_modal').modal('show');

            $('.kj_field_modal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
                $('.kj_field_modal .modal-lg').css('max-width', '');
            });

            $('body').on('keypress', 'input[name=NAME]', function (e) {
                if (e.which == 13) {
                    e.preventDefault();

                    $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').click();
                }
            });

            // Callback voor sluiten modal
            $('.kj_field_modal').find('.modal-footer').find('.kjclosemodal').off().on('click', function (e) {
                e.preventDefault();

                var form = $('.kj_field_modal .modal-body').find('#detailFormTasksCustomMap');

                if (!form.valid()) {
                    return false;
                }

                var rawData = form.serializeArray();
                var formData = new FormData();

                //Alle velden
                rawData.forEach(function(data, index, form) {
                    formData.append(data.name, data.value);
                });

                // Alle checkboxes unchecked als value false
                form.find('input[type=checkbox]:unchecked').each(function(index, data) {
                    formData.append(data.name, 0);
                });

                kjrequest('POST', '/admin/tasks/saveMap', formData, true, function (result) {
                    if (result.success) {
                        location.reload();
                    }
                });
            });
        }
    });
}