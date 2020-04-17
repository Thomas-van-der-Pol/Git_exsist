$(document).ready(function() {
    loadTranslations();

    // Load datatables
    $('.kj_datatable').each(function() {
        if ($(this).data('autoload') == 1) {
            loadDatatable($(this));
        }
    });

    // Bind edit actions
    $('body').on('click', '.editRow', function(e) {
        e.preventDefault();
        loadDetails($(this));
        return false;
    });

    // Bind close actions
    $('body').on('click', '.closeRow', function(e) {
        e.preventDefault();
        closeEditor(e, $(this));
        return false;
    });

    // Bind select actions
    $('body').on('click', '.selectRow', function(e) {
        e.preventDefault();
        selectRow($(this));
        return false;
    });

    $('body').on('click', '.removeCheckedRow', function(e) {
        e.preventDefault();

        var id = $(this).closest('span').data('id');
        var datatableName = $(this).closest('div.selected-rows').data('target');

        removeCheckedRow(datatableName, id);

        return false;
    });

    // Init resize voor full content datatable
    var fullContentDatatable = KTUtil.getByID('kj-datatable-fullcontent');
    if (fullContentDatatable) {
        // Init calculator on resize & datatable load
        $('#' + fullContentDatatable.id).on('kt-datatable--on-layout-updated', function () {
            calculateFullcontentHeight();
        });
        KTUtil.addResizeHandler(calculateFullcontentHeight);
    }

    // Zodat datatables niet opnieuw getekend worden
    // https://keenthemes.com/forums/topic/window-resizeplugin-fullrender-is-it-necessary/#post-14318
    $(window).off("resize");
});

function loadTranslations() {
    kjlocalization.create('Tabellen', [
        {'Verwerkmelding': 'Een moment geduld...'},
        {'Geen resultaten': 'Geen resultaten gevonden'},
        {'Eerste pagina': 'Eerste'},
        {'Vorige pagina': 'Vorige'},
        {'Volgende pagina': 'Volgende'},
        {'Laatste pagina': 'Laatste'},
        {'Meer paginas': 'Meer paginas'},
        {'Pagina nummer': 'Pagina nummer'},
        {'Selecteer pagina grootte': 'Selecteer pagina grootte'},
        {'Aantal resultaten': 'Weergeven {{start}} - {{end}} van {{total}} resultaten'},
        {'Huidig geselecteerde regels': 'Huidig geselecteerde regels'}
    ]);

    kjlocalization.create('Algemeen', [
        {'Succesvol': 'Opgeslagen!'},
        {'Foutmelding': 'Er is een fout opgetreden!'},
        {'Email validatie': 'Gelieve een geldig e-mailadres in te geven'}
    ]);
}

function loadDatatable(div) {
    var datatableName = div.attr('id');
    var name = datatableName + '_configuration';
    var configuration = this[name];

    //Bepaal pagesizeSelect o.b.v. pagesize
    var extraconfigPageSizeSelect = [10, 25, 50, 100, -1];

    if (configuration.customEditButtons) {
        if (configuration.customEditButtons.begin) {
            var aWidth = 0;
            $.each(configuration.customEditButtons.begin, function(index, value) {
                if (value.width) {
                    aWidth += parseInt(value.width);
                } else {
                    aWidth += 41;
                }
            });

            configuration.columns.unshift({
                field: 'customEditButtons_begin',
                title: '',
                width: aWidth,
                sortable: false,
                overflow: 'visible',
                autoHide: false,
                template: function (row) {
                    // if ((configuration.blockEditColumn !== null) && (Boolean(JSON.parse(row[configuration.blockEditColumn])))) {
                    //     return '';
                    // }

                    var template = '';
                    $.each(configuration.customEditButtons.begin, function(index, value) {
                        if (value.HTML) {
                            template += '<span class="mx-1 customEditButton customEditButton_' + index + '">' + value.HTML.replace(/{idField}/g, row[configuration.customid]) + '</span>';
                        }
                    });

                    return template;
                }
            });
        }

        if (configuration.customEditButtons.end) {
            var aWidth = 0;
            $.each(configuration.customEditButtons.end, function(index, value) {
                if (value.width) {
                    aWidth += parseInt(value.width);
                } else {
                    aWidth += 41;
                }
            });

            configuration.columns.push({
                field: 'customEditButtons_end',
                title: '',
                width: aWidth,
                sortable: false,
                overflow: 'visible',
                autoHide: false,
                template: function (row) {
                    // if ((configuration.blockEditColumn !== null) && (Boolean(JSON.parse(row[configuration.blockEditColumn])))) {
                    //     return '';
                    // }

                    var template = '';
                    $.each(configuration.customEditButtons.end, function(index, value) {
                        if (value.HTML) {
                            template += '<span class="mx-1 customEditButton customEditButton_' + index + '">' + value.HTML.replace(/{idField}/g, row[configuration.customid]) + '</span>';
                        }
                    });

                    return template;
                }
            });
        }
    }

    if ((configuration.editable) || (configuration.orderable)) {
        var aWidth = 20;
        if (configuration.editable && configuration.orderable) {
            aWidth = 65;
        }

        configuration.columns.unshift({
            field: 'Actions',
            width: aWidth,
            title: '',
            sortable: false,
            overflow: 'visible',
            autoHide: false,
            template: function(row) {
                var template = '';

                if (configuration.orderable) {
                    template += '<i style="font-size: 20px;" class="la la-bars"></i>';
                }

                if (configuration.editable) {
                    var idField = configuration.customid;
                    //Inline of detailview
                    var editRowURL = configuration.editURL;
                    var editRowIcon = configuration.editIcon;
                    var editRowClassInline = 'editRow';
                    if( ! configuration.editinline ) {
                        editRowClassInline = '';
                        editRowURL = (editRowURL + row[idField]);
                    }

                    if ((configuration.blockEditColumn !== null) && (Boolean(JSON.parse(row[configuration.blockEditColumn])))) {
                        return '';
                    } else {
                        template += '<span class="mx-1">\
                              <a href="' + editRowURL + '" data-id="'+row[idField]+'" class="'+editRowClassInline+' btn btn-bold btn-label-brand btn-sm btn-icon">\
                                  <i class="'+editRowIcon+'"></i>\
                              </a>\
                              <button data-id="'+row[idField]+'" class="closeRow btn btn-bold btn-label-brand btn-sm btn-icon" style="display:none;">\
                                  <i class="la la-close"></i>\
                              </button>\
                          </span>';
                    }
                }

                return template;
            }
        });
    }

    if (configuration.selectable) {
        configuration.columns.push({
            field: 'SelectAction',
            width: 20,
            title: '',
            sortable: false,
            overflow: 'visible',
            autoHide: false,
            template: function(row) {
                var idField = configuration.customid;
                return '\
                          <button data-id="'+row[idField]+'" class="btn btn-bold btn-label-brand btn-sm btn-icon selectRow" title="">\
                              <i class="la la-check"></i>\
                          </button>\
                      ';
            }
        });
    }

    if (configuration.checkable) {
        var idField = configuration.customid;

        configuration.columns.unshift({
            field: idField,
            title: "#",
            width: 5,
            sortable: false,
            type: 'number',
            textAlign: 'center',
            selector: {class: 'kt-checkbox--solid'},
            autoHide: false,
        });
    }

    var url = configuration.source.url;
    if (url.includes('?')) {
        url += '&bc='+Math.random();
    } else {
        url += '?bc='+Math.random();
    }

    var datatable = div.KTDatatable({

        data: {
            type: 'remote',
            source: {
                read: {
                    method: configuration.source.method,
                    url: url,
                    params: {
                        query: configuration.source.params
                    }
                }
            },
            pageSize: configuration.data.pagesize,
            saveState: {
                cookie: false,
                webstorage: true
            },
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true
        },

        // Layout definition
        layout: {
            theme: 'default', // datatable theme
            class: '', // custom wrapper class
            scroll: true, // enable/disable datatable scroll both horizontal and vertical when needed.
            footer: false ,// display/hide footer
            header: configuration.showHeader // display/hide header
        },

        sortable: configuration.sortable,

        pagination: configuration.pagination,

        toolbar: {
            items: {
                pagination: {
                    type: 'default',

                    pages: {
                        desktop: {
                            layout: 'default',
                            pagesNumber: 6
                        },
                        tablet: {
                            layout: 'default',
                            pagesNumber: 3
                        },
                        mobile: {
                            layout: 'compact'
                        }
                    },

                    navigation: {
                        prev: true,
                        next: true,
                        first: true,
                        last: true
                    },

                    pageSizeSelect: extraconfigPageSizeSelect
                }
            }
        },

        // search: {
        //     input: $(configuration.searchinput)
        // },

        columns: configuration.columns,

        rows: {
            autoHide: false,

            beforeTemplate: function(row, data, index) {
                row.attr('data-id', data[configuration.customid]);

                if (configuration.orderable) {
                    row.attr('data-sequence', parseInt(data[configuration.sequenceField]));
                }

                $(document).trigger(datatableName + 'RowCallback',[row, data, index]);
            }
        },

        translate: {
            records: {
                processing: kjlocalization.get('tabellen', 'verwerkmelding'),
                noRecords: kjlocalization.get('tabellen', 'geen_resultaten')
            },
            toolbar: {
                pagination: {
                    items: {
                        default: {
                            first: kjlocalization.get('tabellen', 'eerste_pagina'),
                            prev: kjlocalization.get('tabellen', 'vorige_pagina'),
                            next: kjlocalization.get('tabellen', 'volgende_pagina'),
                            last: kjlocalization.get('tabellen', 'laatste_pagina'),
                            more: kjlocalization.get('tabellen', 'meer_paginas'),
                            input: kjlocalization.get('tabellen', 'pagina_nummer'),
                            select: kjlocalization.get('tabellen', 'selecteer_pagina_grootte')
                        },
                        info: kjlocalization.get('tabellen', 'aantal_resultaten')
                    }
                }
            }
        }
    });

    // Selector zetten i.v.m. aanroepen
    configuration.datatableSelector = datatable;

    // Checkable remember selection
    if (configuration.checkable) {
        configuration.selected = [];

        $('#'+datatableName).on('kt-datatable--on-check', function(event, arguments) {
            var currentTable = $(event.target);

            $.each(arguments, function(i, el) {
                var name = '';
                if (configuration.checkableDescriptionColumn != '') {
                    name = currentTable.find('tr[data-id="'+el+'"]').find('td[data-field='+configuration.checkableDescriptionColumn+']').find('span').text();
                } else {
                    name = currentTable.find('tr[data-id="'+el+'"]').find('td[data-field!='+configuration.customid+']').first().find('span').text();
                }

                var obj = {
                    id: el,
                    name: name
                };

                if (configuration.selected.find(x => x.id == obj.id) === undefined) {
                    configuration.selected.push(obj);
                }
            });

            showCheckedRows(datatableName);
        });

        $('#'+datatableName).on('kt-datatable--on-uncheck', function(event, arguments) {

            $.each(arguments, function(i, el){
                if (configuration.selected.find(x => x.id == el) !== undefined) {
                    configuration.selected = $.grep(configuration.selected, function(x) {
                        return x.id != el;
                    });
                }
            });

            showCheckedRows(datatableName);
        });
    }

    // Layout updated, inits
    $('#'+datatableName).on('kt-datatable--on-layout-updated', function() {
        // Sortable
        if (configuration.orderable) {
            // Class toevoegen aan 1e column
            div.find('td:first-child').each(function() {
                $(this).find('i.la-bars').addClass('kj-drag-handler').css('cursor', 'move');
            });

            // div.find('td:first-child').addClass('kj-drag-handler').css('cursor', 'move').css('width', '30px');
            div.find('td:first-child').css('width', '30px');
            div.find('th:first-child').css('width', '30px'); // Fix om header ook zo klein te maken

            div.find('tbody').sortable({
                handle: ".kj-drag-handler",
                cancel: ".ui-state-disabled",
                start: function (event, ui) {
                    $(ui.item).data("startindex", ui.item.index());
                    $(ui.item).addClass('kj_dragging');
                },
                stop: function (event, ui) {

                    var startIndex = ui.item.data("startindex") + 1;
                    var newIndex = ui.item.index() + 1;

                    if (newIndex != startIndex) {
                        $(document).trigger(datatableName + 'AfterReorder', [
                            ui.item,
                            ui.item.data('id'), // ID
                            ui.item.data('sequence'), // Current sequence
                            startIndex, // Current sequence in table
                            newIndex, // New sequence in table
                            (newIndex - startIndex) // Mutation
                        ]);
                    }

                    $(ui.item).removeClass('kj_dragging');
                }



            }).disableSelection();
        }

        // Checkable
        if (configuration.checkable) {
            setCheckedRowsInTable(datatableName);
        }
    });
    var query = datatable.getDataSourceQuery();

    if (configuration.searchinput != 'unknown-dtbl-input') {
        $(configuration.searchinput).on('keypress', function(e) {
            if (e.which == 13) {
                datatable.search($(this).val(), $(this).attr('name'));
            }
        });
    }

    if (configuration.filters !== null) {
        $.each(configuration.filters, function(index, value) {
            var input = value.input;
            var queryParam = value.queryParam;
            var defaultVal = value.default;
            var format = value.format ? value.format : 'YYYY-MM-DD';

            // Change filter (niet op daterangepicker)
            if (!$(input).hasClass('kjdaterangepicker-picker')) {
                $(input).on('change', function() {
                    // Indien checkbox, dan andere filter toepassen
                    if ($(this).is('input[type=checkbox]')) {
                        datatable.search($(input+':checkbox:checked').length.toString(), queryParam);
                    } else {
                        datatable.search($(this).val(), queryParam);
                    }
                }).val(typeof query.queryParam !== 'undefined' ? query.queryParam : defaultVal);
            }

            // Toepassen van daterangefiler
            $(input).on('apply.daterangepicker', function(ev, picker) {
                var range = {
                    start: picker.startDate.format(format),
                    end: picker.endDate.format(format)
                };

                datatable.search(range, queryParam);
            });//.val(typeof query.queryParam !== 'undefined' ? query.queryParam : defaultVal);

            // Clearen van daterangefiler
            $(input).on('cancel.daterangepicker', function(ev, picker) {
                datatable.search(null, queryParam);
            });//.val(typeof query.queryParam !== 'undefined' ? query.queryParam : defaultVal);

        });
    }

    // Initialisatie add button
    if (configuration.addable === true) {
        $(configuration.addButton).on('click', function(e) {
            e.preventDefault();
            addNew(div.attr('id'));
            return false;
        });
    }
}

function closeEditor(e, closeObj) {
    e.preventDefault();

    // Get parent detail div
    var datatableDiv = closeObj.closest('div .kj_datatable');
    var datatableName = datatableDiv.attr('id');
    var detailDIVname = datatableName+'-detaildiv';
    var detailDiv = $('#'+detailDIVname);

    //Parent TR
    var parentTR = closeObj.closest('tr');

    //Alles sluiten
    datatableDiv.find('tr.kjinline-dtbl-activerow').removeClass('ui-state-disabled');
    datatableDiv.find('tr').removeClass('kjinline-dtbl-activerow');

    detailDiv.slideUp(function() {
        detailDiv.remove();
    });

    //ALLE editknoppen van datatable zichtbaar
    //ALLE closeknoppen van datatable weg
    datatableDiv.find('.editRow').show();
    datatableDiv.find('.closeRow').hide();
}

function loadDetails(linkObj) {
    // Get parent detail div
    var datatableDiv = linkObj.closest('div .kj_datatable');
    var datatableName = datatableDiv.attr('id');

    // Get configuration
    var configName = datatableDiv.attr('id') + '_configuration';
    var configuration = this[configName];

    // Initialize variables
    var detailDIVname = '';

    //Reset alle knoppen
    datatableDiv.find('.editRow').show();
    datatableDiv.find('.closeRow').hide();

    //Parent TR
    var parentTR = linkObj.closest('tr');

    //Active class op parent tr zetten en van rest verwijderen
    datatableDiv.find('tr.kjinline-dtbl-activerow').removeClass('ui-state-disabled');
    datatableDiv.find('tr').removeClass('kjinline-dtbl-activerow');
    parentTR.addClass('kjinline-dtbl-activerow ui-state-disabled');

    var detail_paddingLeft = 0;
    if (configuration.inlineEdit === true) {
        //Detaildiv maken NA parent en indien al 1 bestond die weghalen
        detailDIVname = datatableName+'-detaildiv';
        if ($('#'+detailDIVname).length) {
            $('#'+detailDIVname).remove();
        }

        //Edit icon onzichtbaar en close icon zichtbaar
        var editIconRow = parentTR.find('.editRow');
        editIconRow.hide();
        $('#' + datatableName + ' .closeRow[data-id="'+editIconRow.data('id')+'"]').show();

        //Detaildiv maken EN zetten
        parentTR.after('<div id="'+detailDIVname+'" class="kjinlinedtbldetail ui-state-disabled" style="display:none;"></div>'); //Standaard hidden beginnen

        // Calculate position
        detail_paddingLeft = parentTR.find('td[data-field!="Actions"][data-field!="ID"]').first().position().left;
    } else {
        detailDIVname = configuration.targetElement;
    }

    // Detail div selector
    var detailDiv = $('#'+detailDIVname);

    // Get URL and ID
    var editUrl = linkObj.attr('href');
    var id = linkObj.data('id');

    // Remember last id
    configuration.lastClicked = id;

    // Load view
    $.ajax({
        url: editUrl + id,
        type: 'GET',
        dataType: 'JSON',

        success: function (data) {
            // Close new div
            cancelNew(datatableName + '-new');

            // Load detail form
            detailDiv.css('padding-left', detail_paddingLeft);
            detailDiv.html(data.viewDetail);

            // Bind save events
            if (configuration.saveUrl > '') {
                $('#' + detailDIVname + ' .kj_save').on('click', function (e) {
                    e.preventDefault();

                    save($(this), configuration.saveUrl, configuration.parentid, (configuration.inlineEdit === true), detailDiv, function(data) {
                        $(document).trigger(datatableName + 'AfterSave',[data]);

                        configuration.datatableSelector.reload(null, false);
                    });
                });
            }

            // Load child datatables
            $('#'+detailDIVname+' .kj_datatable').each(function() {
                loadDatatable($(this));
            });

            // Load fields
            loadDropdowns();
            loadDatePickers();
            loadDateTimePickers();
            loadKJPostcodeLookups();

            setMaterialActiveLabels(detailDiv);

            // Callback to rebind events
            $(document).trigger(datatableName + 'AfterLoad',[detailDiv]);

            detailDiv.slideDown(function() {
                var firstElementje = $('#' + detailDIVname + ' input:not(.datepicker):not(.datetimepicker):not(.kjdaterangepicker-picker):not(:hidden)').first();
                if (firstElementje !== undefined) {
                    firstElementje.focus();
                }

                // Trigger the after animation event.
                $(document).trigger(datatableName + 'AfterLoadAnimation', [detailDiv]);
            });
        }
    });

}

function addNew(datatableName) {

    // Get configuration
    var name = datatableName + '_configuration';
    var configuration = this[name];

    var selectorName = datatableName + '-new';
    if (configuration.targetElement !== null) {
        selectorName = configuration.targetElement;
    }

    var targetElement = $('#' + selectorName);
    // Load view
    $.ajax({
        url: configuration.editURL + configuration.newrecordid,
        type: 'GET',
        dataType: 'JSON',

        success: function (data) {
            // Close open rows
            $('#'+datatableName).find('.closeRow').click();

            // Load detail form
            targetElement.html(data.viewDetail);

            // Bind save events
            if (configuration.saveUrl > '') {
                $('#' + selectorName + ' .kj_save').on('click', function (e) {
                    e.preventDefault();
                    save($(this), configuration.saveUrl, configuration.parentid, (configuration.inlineEdit === true), targetElement, function(data) {
                        $(document).trigger(datatableName + 'AfterSave',[data]);

                        configuration.datatableSelector.reload(null, false)
                    });
                });
            }

            // Bind cancel event
            if (configuration.addable) {
                $('#' + selectorName + ' .kj_cancel').on('click', function (e) {
                    e.preventDefault();

                    cancelNew(selectorName);
                });
            }

            // Load fields
            loadDropdowns();
            loadDatePickers();
            loadDateTimePickers();
            loadKJPostcodeLookups();

            setMaterialActiveLabels(targetElement);

            // Callback to rebind events
            $(document).trigger(datatableName + 'AfterLoad',[targetElement]);
            $(document).trigger(datatableName + 'AfterNew',[targetElement]);

            targetElement.slideDown(function() {
                var firstElementje = $('#' + selectorName + ' input:not(.datepicker):not(.datetimepicker):not(.kjdaterangepicker-picker):not(:hidden)').first();
                if (firstElementje !== undefined) {
                    firstElementje.focus();
                }

                $(document).trigger(datatableName + 'AfterLoadAnimation', [targetElement]);
                $(document).trigger(datatableName + 'AfterNewAnimation', [targetElement]);
            });
        }
    });
}

function cancelNew(datatableName) {
    // Container voor nieuw toe te voegen item ophalen
    var targetElement = $('#' + datatableName);

    // Container sluiten en leegmaken
    targetElement.slideUp(function() {
        targetElement.empty();
    });
}

function save(saveBtn, saveUrl, parentId, closeAfterSave, targetElement, callback) {
    // Validation rule voor e-mail overschrijven
    // Let op: is tijdelijk tot nieuwe update Metronic/jQuery validation
    // Kapot sinds metronic 5.1
    $.validator.addMethod("email", function(value, element) {
        var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
        return this.optional( element ) || pattern.test(value);
    }, kjlocalization.get('algemeen', 'email_validatie'));

    // Form ophalen
    var form = saveBtn.closest('form');

    // Validate
    form.validate({
        errorPlacement: function(error, element) { // render error placement for each input type
            var element = $(element);

            var group = valGetParentContainer(element);
            var help = group.find('.form-text');

            if (group.find('.valid-feedback, .invalid-feedback').length !== 0) {
                return;
            }

            element.addClass('is-invalid');
            error.addClass('invalid-feedback');

            if (help.length > 0) {
                help.before(error);
            } else {
                if (element.closest('.bootstrap-select').length > 0) {     //Bootstrap select
                    error.appendTo(element.closest('.bootstrap-select').parent()); // KJ aanpassing voor juiste positie select
                    // element.closest('.bootstrap-select').find('.bs-placeholder').after(error);
                } else if (element.closest('.input-group').length > 0) {   //Bootstrap group
                    error.appendTo(element.closest('.input-group')); // KJ Aanpassing zodat deze achter de addons komt
                    // element.after(error);
                } else {                                                   //Checkbox & radios
                    if (element.is(':checkbox')) {
                        element.closest('.kt-checkbox').find('> span').after(error);
                    } else {
                        element.after(error);
                    }
                }
            }
        }
    });

    if (!form.valid()) {
        return;
    }

    var rawdata = form.serializeArray();
    var formdata = new FormData();

    //Alle velden
    rawdata.forEach(function(data, index, form) {
        formdata.append(data.name, data.value);
    });

    // Alle checkboxes unchecked als value false
    form.find('input[type=checkbox]:unchecked').each(function(index, data) {
        formdata.append(data.name, 0);
    });

    //Alle velden type bestand
    form.find('input[type=file]').each(function(index, data) {
        if( data.files[0]) {
            formdata.append(data.name, data.files[0]);
        }
    });

    //Eventueel partentId
    if (parentId !== null) {
        formdata.append('PARENTID',parentId);
    }

    kjrequest('POST', saveUrl, formdata, false, function (data) {
        if ((closeAfterSave) && (targetElement !== null)) {
            targetElement.slideUp(function() {
                targetElement.empty();

                // Row resetten
                var datatableDiv = targetElement.closest('div .kj_datatable');
                datatableDiv.find('tr').removeClass('kjinline-dtbl-activerow');
                datatableDiv.find('.editRow').show();
                datatableDiv.find('.closeRow').hide();
            });
        }
        if (callback != null) {
            callback(data);
        }
    }, function (data) {
        if (callback != null) {
            callback(data);
        }
    });
}

function selectRow(linkObj) {
    var selectedID = linkObj.data('id');

    var datatable = linkObj.closest('div .kj_datatable');
    var datatableName = datatable.attr('id');

    $(document).trigger(datatableName + 'AfterSelect', [selectedID, linkObj]);
}

function showCheckedRows(datatableName) {
    // Get configuration
    var name = datatableName + '_configuration';
    var configuration = this[name];

    var container = $('.selected-rows[data-target="'+datatableName+'"]');

    if (configuration !== undefined) {
        var html = '';

        if (configuration.selected.length > 0) {
            html += '<p class="kt-font-bold">' + kjlocalization.get('tabellen', 'huidig_geselecteerde_regels') + ':</p>';
        }

        $.each(configuration.selected, function(i, el) {
            html += '<span class="kt-badge kt-badge--unified-brand kt-badge--inline kt-badge--rounded kt-badge--bold mr-2 mb-2" data-id="'+el.id+'">'+el.name+'&nbsp;<i class="la la-close removeCheckedRow" style="cursor: pointer;"></i></span>';
        });

        container.html(html);
    }
}

function setCheckedRowsInTable(datatableName) {
    // Get configuration
    var name = datatableName + '_configuration';
    var configuration = this[name];

    if (configuration !== undefined) {
        $.each(configuration.selected, function(i, el) {
            configuration.datatableSelector.setActive(el.id.toString());
        });
    }
}

function removeCheckedRow(datatableName, id) {
    // Get configuration
    var name = datatableName + '_configuration';
    var configuration = this[name];

    if (configuration.selected.find(x => x.id == id) !== undefined) {
        configuration.selected = $.grep(configuration.selected, function(x) {
            return x.id != id;
        });

        configuration.datatableSelector.setInactive(id.toString());
    }

    showCheckedRows(datatableName);
}
function getCheckedRows(datatableName) {
    // Get configuration
    var name = datatableName + '_configuration';
    var configuration = this[name];

    if (configuration !== undefined) {
        return configuration.selected.map(function(element) { return element.id });
    }
}

function resetFilters(datatableName)
{
    // Get configuration
    var name = datatableName + '_configuration';
    var configuration = this[name];

    if (configuration !== undefined) {

        var query = configuration.datatableSelector.getDataSourceQuery();

        // Reset searchInput (no default value)
        if (configuration.searchinput !== 'unknown-dtbl-input') {
            var input = $(configuration.searchinput);

            // Reset value
            input.val('');
            input.trigger('change');

            // Delete from query
            delete query[input.attr('id')];
        }

        // Loop through filters, reset with default value
        if (configuration.filters !== null) {
            $.each(configuration.filters, function(index, value) {
                var input = $(value.input);
                var queryParam = value.queryParam;
                var defaultVal = value.default;
                var format = value.format ? value.format : 'YYYY-MM-DD';
                var isDateRangePicker = false;

                // If daterange picker, get start- and enddate as default value
                if (input.hasClass('kjdaterangepicker-picker')) {
                    isDateRangePicker = true;
                    var startDate = input.data('start-date');
                    var endDate = input.data('end-date');

                    if ((startDate !== undefined) && (endDate !== undefined)) {
                        defaultVal = startDate + ' / ' + endDate;

                        input.data('daterangepicker').setStartDate(startDate);
                        input.data('daterangepicker').setEndDate(endDate);
                    }
                }

                // Reset value
                if (input.is(':checkbox')) {
                    input.prop('checked', defaultVal);

                    if (input.hasClass('setToggleSwitch')) {
                        setToggleSwitchStyle(input);
                    }
                } else {
                    input.val(defaultVal);
                }
                input.trigger('change');

                // Reset in query
                if (isDateRangePicker) {
                    query[queryParam] = {
                        start: input.data('daterangepicker').startDate.format(format),
                        end: input.data('daterangepicker').endDate.format(format)
                    };
                } else {
                    query[queryParam] = defaultVal;
                }

                if (input.is('select')) {
                    input.selectpicker('refresh');
                }
            });
        }

        // Remove empty element from arrays
        $.each(query, function (k, v) {
            if (v === '' || $.isEmptyObject(v)) {
                delete query[k];
            }
        });

        // Set filters on datatable
        configuration.datatableSelector.setDataSourceQuery(query);
    }
}

function calculateFullcontentHeight() {
    var fullContentDatatable = KTUtil.getByID('kj-datatable-fullcontent');
    if (fullContentDatatable) {
        // BasePortlet klein beetje aanpassen
        var portlet = KTUtil.getByClass('kt-portlet');
        KTUtil.css(portlet, 'margin-bottom', '0px');

        // 1. Window height ophalen
        var height = KTLayout.getContentHeight() + 20; // + 20 voor de porlet margin die eraf word gehaald en 1 random pixel
        if (KTUtil.isInResponsiveRange('desktop')) {
            height = height + 1;
        }

        // 2. Haal de subheader van de hoogte af
        var subheader = KTUtil.getByID('kt_subheader');
        if (subheader) {
            height = height - parseFloat(KTUtil.css(subheader, 'height'));
            if (KTUtil.isInResponsiveRange('desktop') == false) {
                height = height - parseFloat(KTUtil.css(subheader, 'padding-top'));
                height = height - parseFloat(KTUtil.css(subheader, 'padding-bottom'));
            }
        }

        // 3. Haal de padding van de content weg
        var content = KTUtil.getByID('kt_content');
        if (content) {
            height = height - parseFloat(KTUtil.css(content, 'padding-top'));
            height = height - parseFloat(KTUtil.css(content, 'padding-bottom'));
        }

        // 4. Als laatste haal de hoogte van de titelbar weg
        var tablehead = KTUtil.find(fullContentDatatable, '.kt-datatable__head');
        if (tablehead) {
            height = height - parseFloat(KTUtil.css(tablehead, 'height'));
        }

        if (KTUtil.isInResponsiveRange('desktop') == false) {
            var pager = KTUtil.find(fullContentDatatable, '.kt-datatable__pager');
            if (pager) {
                height = height - parseFloat(KTUtil.css(pager, 'padding-top'));
                height = height - parseFloat(KTUtil.css(pager, 'padding-bottom'));
            }
        }

        var tablebody = KTUtil.find(fullContentDatatable, '.kt-datatable__body');
        if (tablebody) {
            KTUtil.css(tablebody, 'height', height + 'px');
        }
    }
}