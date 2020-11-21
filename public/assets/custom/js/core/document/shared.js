Dropzone.autoDiscover = false;

var baseDocumentUrl = '/document';

$(document).ready(function() {

    $('body').on('change', '.autoSaveDocument', function(e) {
        e.preventDefault();

        var id = $(this).data('id');

        var formData = new FormData();
        formData.append('ID', id);
        formData.append($(this).attr('name'), this.checked);

        kjrequest('POST', baseDocumentUrl, formData, false,
            null,
            function (data) {
                reloadDocuments(getDropzone());
            }
        );
    });

    $('body').on('mouseenter', '.documentInfo', function(e) {
        $(this).popover('show');
    });

    $('body').on('mouseleave', '.documentInfo', function(e) {
        $(this).popover('hide');
    });

    loadDropzone();

    // Group selection
    $('body').on('click', 'input[name="MARK_ALL_DOCUMENTS"]', function() {
        var listEl = KTUtil.getByID('documentUploader');
        var items = KTUtil.findAll(listEl, '.kt-widget4__item');

        for (var i = 0, j = items.length; i < j; i++) {
            var item = items[i];
            var checkbox = KTUtil.find(item, 'input[name="MARK_DOCUMENT"]');
            if (checkbox != null) {
                checkbox.checked = this.checked;

                if (this.checked) {
                    KTUtil.addClass(item, 'kt-widget4__item--selected');
                } else {
                    KTUtil.removeClass(item, 'kt-widget4__item--selected');
                }
            }
        }

        $('.deleteFiles').prop('disabled', !(getCheckedDocuments().length > 0));
        $('.renameFile').prop('disabled', !(getCheckedDocuments().length === 1));
    });

    $('body').on('click', 'input[name="MARK_DOCUMENT"]', function() {
        var item = $(this).closest('.kt-widget4__item');

        if (this.checked) {
            item.addClass('kt-widget4__item--selected');
        } else {
            item.removeClass('kt-widget4__item--selected');
        }

        $('.deleteFiles').prop('disabled', !(getCheckedDocuments().length > 0));
        $('.renameFile').prop('disabled', !(getCheckedDocuments().length === 1));
    });

    $('body').on('click', '.openFolder', function(e) {
        e.preventDefault();

        currentFolder = $(this).data('folder');
        reloadDocuments(getDropzone());
    });

    $('body').on('click', '.requestDocument', function(e) {
        e.preventDefault();

        var id = $(this).data('id');

        var formdata = new FormData();
        formdata.append('id', id);
        formdata.append('uploader_table', $('input[name=uploader_table]').val());
        formdata.append('uploader_item', $('input[name=uploader_item]').val());

        kjrequest('POST', baseDocumentUrl + '/request', formdata, true,
            function (data) {
                var method = 'GET';
                var url = baseDocumentUrl + '/download?token=' + data.request_token;

                if (data.try_communicator == true) {
                    if ((window["kjcommunicator"] != undefined) && (kjcommunicator.installed === true)) {
                        // Download file using communicator
                        kjcommunicator.openDocument(data.communicator_url, data.request_token, data.communicator_title);
                    } else {
                        downloadFileAjax(method, url);
                    }
                } else {
                    downloadFileAjax(method, url);
                }
            },
            null
        );
    });

    $('body').on('click', '.addDirectory', async function(e) {
        e.preventDefault();

        const { value: directoryName } = await Swal.fire({
            title: kjlocalization.get('documenten', 'voer_mapnaam_in'),
            input: 'text',
            showCancelButton: true,
            inputValidator: (value) => {
                if (!value) {
                    return kjlocalization.get('documenten', 'mapnaam_mag_niet_leeg_zijn')
                }
            }
        });

        if (directoryName) {
            var formData = new FormData();
            formData.append('fk_table', $('input[name=FK_TABLE]').val());
            formData.append('fk_item', $('input[name=FK_ITEM]').val());
            formData.append('uploader_table', $('input[name=uploader_table]').val());
            formData.append('uploader_item', $('input[name=uploader_item]').val());
            formData.append('directory', currentFolder.replace(/\//g, '\\'));
            formData.append('new_directory_name', directoryName);

            kjrequest('POST', baseDocumentUrl + '/add-folder', formData, true,
                function(data) {
                    reloadDocuments(getDropzone());
                },
                null
            );
        }
    });

    $('body').on('click', '.renameFile', async function(e) {
        e.preventDefault();

        var checkedDocuments = getCheckedDocuments(true);

        if (!checkedDocuments.length === 1) {
            swal.fire({
                text: kjlocalization.get('documenten', 'selecteer_minimaal_een_regel'),
                type: 'error'
            });

            return false;
        }

        var defaultValue = checkedDocuments[0].input.find('.kt-widget4__title').text();

        const { value: newFilename } = await Swal.fire({
            title: kjlocalization.get('documenten', 'voer_naam_in'),
            input: 'text',
            inputValue: defaultValue,
            showCancelButton: true,
            inputValidator: (value) => {
                if (!value) {
                    return kjlocalization.get('documenten', 'naam_mag_niet_leeg_zijn')
                }
            }
        });

        if (newFilename) {
            var formData = new FormData();
            formData.append('selected_document_id', checkedDocuments[0].id);
            formData.append('selected_document_folder', checkedDocuments[0].folder);
            formData.append('selected_document_type', checkedDocuments[0].type);

            formData.append('fk_table', $('input[name=FK_TABLE]').val());
            formData.append('fk_item', $('input[name=FK_ITEM]').val());
            formData.append('current_directory', currentFolder.replace(/\//g, '\\'));
            formData.append('new_filename', newFilename);

            kjrequest('POST', baseDocumentUrl + '/rename', formData, true,
                function(data) {
                    reloadDocuments(getDropzone());
                },
                null
            );
        }
    });

    $('body').on('click', '.deleteFiles', function(e) {
        e.preventDefault();

        var checkedDocuments = getCheckedDocuments();

        if (checkedDocuments.length === 0) {
            swal.fire({
                text: kjlocalization.get('documenten', 'selecteer_minimaal_een_regel'),
                type: 'error'
            });

            return false;
        }

        var formData = new FormData();
        formData.append('documents', JSON.stringify(checkedDocuments));
        formData.append('fk_table', $('input[name=FK_TABLE]').val());
        formData.append('fk_item', $('input[name=FK_ITEM]').val());
        formData.append('after_remove', $('input[name=after_remove]').val());

        swal.fire({
            title: kjlocalization.get('documenten', 'document_verwijderen_titel'),
            text: kjlocalization.get('documenten', 'document_verwijderen_tekst'),
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: kjlocalization.get('algemeen', 'doorgaan'),
            cancelButtonText: kjlocalization.get('algemeen', 'annuleren')
        }).then(function(result) {
            if (result.value) {
                kjrequest('POST', baseDocumentUrl + '/delete', formData, true,
                    function(data) {
                        reloadDocuments(getDropzone());
                    },
                    null
                );
            } else if (result.dismiss === 'cancel') {
                // result.dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
            }
        });
    });

    $('body').on('click', '.reloadButton', function(e) {
        e.preventDefault();
        reloadDocuments(getDropzone());
    });
});

function loadDropzone()
{
    $('div#documentUploader').dropzone({
        url: baseDocumentUrl + '/upload',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        params: {
            fk_table: $('input[name=FK_TABLE]').val(),
            fk_item: $('input[name=FK_ITEM]').val(),
            uploader_table: $('input[name=uploader_table]').val(),
            uploader_item: $('input[name=uploader_item]').val(),
            document_library: $('input[name=document_library]').val(),
            after_save: $('input[name=after_save]').val()
        },
        maxFilesize: 10, // MB
        clickable: '.addFile',
        previewTemplate: $('div#dropzone-template-preview').html(),

        init: function () {
            var dropzone = this;

            reloadDocuments(dropzone);

            this.on("sending", function(file, xhr, formData) {
                formData.append('directory', currentFolder.replace(/\//g, '\\'));
            });

            this.on("success", function(file, response) {
                // Remove 'this folder is empty'
                var emptyFolderBar = $(dropzone.element).find('.emptyFolderBar');
                if (emptyFolderBar.length > 0) {
                    emptyFolderBar.remove();
                }

                // Add file
                $(file.previewElement).find('img[name=THUMB]').attr('src', '/assets/themes/demo1/media/files/'+response.document.FILETYPE.toLowerCase()+'.svg');
                $(file.previewElement).find('.kt-widget4__title').data('id', response.document.ID);

                var checkbox = $(file.previewElement).find('input[name="MARK_DOCUMENT"]');
                // var available_client = $(file.previewElement).find('input[name=AVAILABLE_CLIENT]');
                // var available_family = $(file.previewElement).find('input[name=AVAILABLE_FAMILY]');

                if (response.document.FILETYPE.toLowerCase() !== 'dir') {
                    $(file.previewElement).find('.kt-widget4__title').addClass('requestDocument');
                    if (checkbox.length) {
                        checkbox.attr('data-id', response.document.ID);
                    }

                    // available_client.data("id", response.document.ID);
                    // available_family.data("id", response.document.ID);
                    // if (response.document.AVAILABLE_CLIENT == 1) {
                    //     available_client.prop("checked", response.document.AVAILABLE_CLIENT);
                    // }
                    // if (response.document.AVAILABLE_FAMILY == 1) {
                    //     available_family.prop("checked", response.document.AVAILABLE_FAMILY);
                    // }
                } else {
                    $(file.previewElement).addClass('droppable');
                    $(file.previewElement).find('.kt-widget4__title').addClass('openFolder');

                    if (checkbox.length) {
                        checkbox.attr('data-folder', response.document.DIRECTORY);
                    }

                    // available_client.closest('.kt-checkbox').remove();
                    // available_family.closest('.kt-checkbox').remove();
                }

                // Check editable
                if ((isEditable() === false) && (checkbox.length)) {
                    $(file.previewElement).find('.checkboxField').hide();
                }

                $(file.previewElement).find('.kt-widget4__title').text(response.document.TITLE);
                $(file.previewElement).find('.documentInfo').attr('data-content', response.document.DOCUMENT_INFORMATION);

                $(file.previewElement).find('.fileSize').text(response.document.FILESIZE_FORMATTED);
                $(file.previewElement).find('.fileType').text(response.document.FILETYPE_FORMATTED);
                $(file.previewElement).find('.fileModified').text(response.document.LASTMODIFIED_FORMATTED);

                $(file.previewElement).find('input[name=ID]').val(response.document.ID);

                // Load drag'n'drop
                loadDragDrop();
            });

            this.on("error", function(file, errorMessage) {
                // Remove item from documentexplorer
                $(file.previewElement).remove();

                // Show message
                swal.fire({
                    title: 'Something went wrong',
                    text: (errorMessage.message || 'Unknown reason'),
                    type: 'error'
                });
            });
        },

        removedfile: function(file) {

            var document = $.map($(file.previewElement).find('input[name="MARK_DOCUMENT"]'), function(c){
                return {
                    id: ($(c).data('id') || -1),
                    folder: ($(c).data('folder') || ''),
                    type: ((($(c).data('id') || -1) === -1) ? 'dir' : 'file')
                };
            });

            var formData = new FormData();
            formData.append('documents', JSON.stringify(document));
            formData.append('fk_table', $('input[name=FK_TABLE]').val());
            formData.append('fk_item', $('input[name=FK_ITEM]').val());
            formData.append('after_remove', $('input[name=after_remove]').val());

            swal.fire({
                title: kjlocalization.get('documenten', 'document_verwijderen_titel'),
                text: kjlocalization.get('documenten', 'document_verwijderen_tekst'),
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: kjlocalization.get('algemeen', 'doorgaan'),
                cancelButtonText: kjlocalization.get('algemeen', 'annuleren')
            }).then(function(result) {
                if (result.value) {
                    kjrequest('POST', baseDocumentUrl + '/delete', formData, true,
                        function(data) {
                            file.previewElement.remove();
                        },
                        null
                    );
                } else if (result.dismiss === 'cancel') {
                    // result.dismiss can be 'cancel', 'overlay',
                    // 'close', and 'timer'
                }
            });
        }
    });
}

function getDropzone()
{
    return Dropzone.forElement('#documentUploader');
}

function isEditable()
{
    return ($('input[name=documents_editable]').val() == 1);
}

function reloadDocuments(dropzone)
{
    // Reload items
    var formdata = new FormData();
    formdata.append('fk_table', $('input[name=FK_TABLE]').val());
    formdata.append('fk_item', $('input[name=FK_ITEM]').val());
    formdata.append('dir', currentFolder.replace(/\//g, '\\'));
    formdata.append('base_dir', baseFolder);
    formdata.append('uploader_table', $('input[name=uploader_table]').val());
    formdata.append('uploader_item', $('input[name=uploader_item]').val());
    formdata.append('document_library', $('input[name=document_library]').val());

    kjrequest('POST', baseDocumentUrl + '/retrieve', formdata, true,
        function (data) {
            // Clear existing items
            $(dropzone.element).find('.kt-widget4__item').remove();

            var mockFile = {};

            // Header bar
            mockFile = {
                id: -1,
                name: 'headerBar'
            };
            dropzone.options.addedfile.call(dropzone, mockFile);
            // dropzone.options.thumbnail.call(dropzone, mockFile, '/' + document.FILEPATH);
            mockFile.previewElement.classList.add('dz-complete');

            $(mockFile.previewElement).addClass('kt-font-boldest header');
            $(mockFile.previewElement).find('.documentInfo').hide();
            $(mockFile.previewElement).find('.kt-widget4__title').replaceWith('<span>'+kjlocalization.get('documenten', 'naam')+'</span>');
            var checkbox = $(mockFile.previewElement).find('input[name="MARK_DOCUMENT"]');
            if (checkbox.length) {
                checkbox.attr('name', 'MARK_ALL_DOCUMENTS');
            }
            // Check editable
            if ((isEditable() === false) && (checkbox.length)) {
                $(mockFile.previewElement).find('.checkboxField').hide();
            }

            $(mockFile.previewElement).find('.fileSize').text(kjlocalization.get('documenten', 'grootte'));
            $(mockFile.previewElement).find('.fileType').text(kjlocalization.get('documenten', 'type'));
            $(mockFile.previewElement).find('.fileModified').text(kjlocalization.get('documenten', 'gewijzigd_op'));
            $(mockFile.previewElement).find('.no_header').empty();
            $(mockFile.previewElement).find('[data-dz-remove]').remove();

            // Directories and files
            $.each(data.items, function (index, document) {
                mockFile = {
                    id: document.ID,
                    name: document.TITLE
                };
                dropzone.options.addedfile.call(dropzone, mockFile);
                dropzone.options.thumbnail.call(dropzone, mockFile, '/' + document.FILEPATH);
                mockFile.previewElement.classList.add('dz-complete');

                $(mockFile.previewElement).find('img[name=THUMB]').attr('src', '/assets/themes/demo1/media/files/'+document.FILETYPE.toLowerCase()+'.svg');
                $(mockFile.previewElement).find('.kt-widget4__title').data('id', document.ID);

                var checkbox = $(mockFile.previewElement).find('input[name="MARK_DOCUMENT"]');
                // var available_client = $(mockFile.previewElement).find('input[name=AVAILABLE_CLIENT]');
                // var available_family = $(mockFile.previewElement).find('input[name=AVAILABLE_FAMILY]');

                if (document.FILETYPE.toLowerCase() !== 'dir') {
                    $(mockFile.previewElement).find('.kt-widget4__title').addClass('requestDocument');
                    $(mockFile.previewElement).find('.documentInfo').attr('data-content', document.DOCUMENT_INFORMATION);

                    if (checkbox.length) {
                        checkbox.attr('data-id', document.ID);
                    }

                    // available_client.data("id", document.ID);
                    // available_family.data("id", document.ID);
                    // if (document.AVAILABLE_CLIENT == 1) {
                    //     available_client.prop("checked", document.AVAILABLE_CLIENT);
                    // }
                    // if (document.AVAILABLE_FAMILY == 1) {
                    //     available_family.prop("checked", document.AVAILABLE_FAMILY);
                    // }
                } else {
                    if (document.ID == -1) {
                        $(mockFile.previewElement).addClass('exclude-drag');
                        $(mockFile.previewElement).find('.checkboxField').empty();
                        $(mockFile.previewElement).find('[data-dz-remove]').remove();
                    }
                    $(mockFile.previewElement).addClass('droppable');
                    $(mockFile.previewElement).find('.kt-widget4__title').addClass('openFolder');
                    $(mockFile.previewElement).find('.kt-widget4__title').data('folder', document.DIRECTORY);
                    $(mockFile.previewElement).find('.documentInfo').hide();

                    if (checkbox.length) {
                        checkbox.attr('data-folder', document.DIRECTORY);
                    }

                    // available_client.closest('.kt-checkbox').remove();
                    // available_family.closest('.kt-checkbox').remove();
                }

                // Check editable
                if ((isEditable() === false) && (checkbox.length)) {
                    $(mockFile.previewElement).find('.checkboxField').hide();
                }

                $(mockFile.previewElement).find('.kt-widget4__title').text(document.TITLE);

                $(mockFile.previewElement).find('.fileSize').text(document.FILESIZE_FORMATTED);
                $(mockFile.previewElement).find('.fileType').text(document.FILETYPE_FORMATTED);
                $(mockFile.previewElement).find('.fileModified').text(document.LASTMODIFIED_FORMATTED);

                $(mockFile.previewElement).find('input[name=ID]').val(document.ID);

                if (document.DELETE_PERMISSION === false) {
                    $(mockFile.previewElement).find('[data-dz-remove]').remove();
                }
            });

            if (data.item_count == 0) {
                // Dummy 'this folder is empty'
                mockFile = {
                    id: -1,
                    name: 'emptyFolderBar'
                };
                dropzone.options.addedfile.call(dropzone, mockFile);
                mockFile.previewElement.classList.add('dz-complete');
                mockFile.previewElement.classList.add('exclude-drag');
                mockFile.previewElement.classList.add('emptyFolderBar');

                $(mockFile.previewElement).find('.kt-widget4__title').replaceWith('<div class="text-center">'+kjlocalization.get('documenten', 'deze_map_is_leeg')+'</div>');
                $(mockFile.previewElement).find('.draggable_remove_at_clone').remove();
            }

            // Set breadcrumbs
            setBreadcrumbs();

            // Load drag'n'drop
            loadDragDrop();

            // Set delete button
            $('.deleteFiles').prop('disabled', true);
            $('.renameFile').prop('disabled', true);
        },
        null
    );
}

function loadDragDrop()
{
    // Check editable
    if (isEditable() === false) {
        return false;
    }

    // Set draggable
    $('#documentContainer').find('.kt-widget4__item:not(.header):not(.exclude-drag)').draggable({
        revert: 'invalid',
        helper: "clone",
        scroll: false,
        start: function(event, ui) {
            // Remove un-interesting items from clone
            ui.helper.find('.draggable_remove_at_clone').remove();
            ui.helper.find('.kt-widget4__title').removeClass('requestDocument');

            // Set cursor at center of clone
            $(this).draggable('instance').offset.click = {
                left: Math.floor(ui.helper.width() / 2),
                top: Math.floor(ui.helper.height() / 2)
            };
        }
    });

    // Set droppable
    $('#documentContainer').find('.droppable').droppable({
        accept: '.kt-widget4__item',
        drop: function(event, ui) {
            var destFolder = $(event.target).find('.openFolder').data('folder');
            var documentId = ui.draggable.find('.kt-widget4__title').data('id') || 0;
            var sourceFolder = ui.draggable.find('.kt-widget4__title').data('folder') || '';

            // Remove draggable item
            ui.draggable.remove();

            // Post change
            var formData = new FormData();
            formData.append('id', documentId);
            formData.append('current_directory', currentFolder.replace(/\//g, '\\'));
            formData.append('dest_directory', destFolder);
            formData.append('source_directory', sourceFolder);
            formData.append('fk_table', $('input[name=FK_TABLE]').val());
            formData.append('fk_item', $('input[name=FK_ITEM]').val());

            kjrequest('POST', baseDocumentUrl + '/move', formData, true,
                null,
                function(data) {
                    reloadDocuments(getDropzone());
                }
            );
        }
    });
}

function setBreadcrumbs()
{
    var folder = currentFolder.replace(baseFolder, '').replace(baseFolder.replace(/\//g, '\\'), '').replace(/^\\+/, '');
    var folderParts = [];
    var homeClass = 'droppable';
    if (folder !== '') {
        folderParts = folder.split('\\');
    } else {
        homeClass = 'active';
    }
    var currentPath = baseFolder;

    var html = '<div class="breadcrumb_item"><a href="javascript:;" class="openFolder" data-folder="'+baseFolder.replace(/\//g, '\\')+'"><i class="flaticon2-shelter"></i></a></div>';
    html += '<div class="breadcrumb_item '+homeClass+'"><a href="javascript:;" class="openFolder" data-folder="'+baseFolder.replace(/\//g, '\\')+'">'+kjlocalization.get('documenten', 'home')+'</a></div>';

    $.each(folderParts, function (index, folder) {
        if (currentPath !== '') {
            currentPath += '\\';
        }
        currentPath += folder;

        if (index === (folderParts.length - 1)) {
            html += '<div class="breadcrumb_item active"><a href="javascript:;">'+folder+'</a></div>';
        } else {
            html += '<div class="breadcrumb_item droppable"><a href="javascript:;" class="openFolder" data-folder="'+currentPath+'">'+folder+'</a></div>';
        }
    });

    $('#breadcrumb-dropzone').html(html);
}

function getCheckedDocuments(includeItem = false)
{
    return $.map($('input[name="MARK_DOCUMENT"]:checked'), function(c) {
        var item = $(c).closest('div.kt-widget4__item');
        var invalid = (item.hasClass('dz-error'));

        if (!invalid) {
            return {
                id: ($(c).data('id') || -1),
                folder: ($(c).data('folder') || ''),
                type: ((($(c).data('id') || -1) === -1) ? 'dir' : 'file'),
                input: ((includeItem === true) ? $(c).closest('.kt-widget4__item') : null)
            };
        }
    });
}