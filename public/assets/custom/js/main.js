window.log = function() {
    log.history = log.history || [];   // store logs to an array for reference
    log.history.push(arguments);
    arguments.callee = arguments.callee.caller;
    if (this.console)
        console.log(Array.prototype.slice.call(arguments));
};

function openKJPopup(url, factorWidth, factorHeigth) {
    if(factorWidth === undefined) factorWidth = 0.9;
    if(factorHeigth === undefined) factorHeigth = 0.8;

    var newWidth = screen.width * factorWidth;
    var newHeight = screen.height * factorHeigth;
    var newleft = (screen.width/2)-(newWidth/2);
    var newtop = (screen.height-newHeight) / 4;

    window.open(url, '', 'height='+newHeight+',width='+newWidth+',top='+newtop+',left='+newleft)
}

function downloadFileAjax(method, url)
{
    var xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    xhr.responseType = 'arraybuffer';
    xhr.onload = function () {
        if (this.status === 200) {
            var filename = "";
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
            }
            var type = xhr.getResponseHeader('Content-Type');

            var blob = typeof File === 'function'
                ? new File([this.response], filename, { type: type })
                : new Blob([this.response], { type: type });
            if (typeof window.navigator.msSaveBlob !== 'undefined') {
                // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
                window.navigator.msSaveBlob(blob, filename);
            } else {
                var URL = window.URL || window.webkitURL;
                var downloadUrl = URL.createObjectURL(blob);

                if (filename) {
                    // use HTML5 a[download] attribute to specify filename
                    var a = document.createElement("a");
                    // safari doesn't support this yet
                    if (typeof a.download === 'undefined') {
                        window.location = downloadUrl;
                    } else {
                        a.href = downloadUrl;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                    }
                } else {
                    window.location = downloadUrl;
                }

                setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 100); // cleanup
            }
        }
    };
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // stopKJLoader();
        }
    };
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    // startKJLoader({funVersion:true, CrazyLoader:true});
    xhr.send();
}

function loadUppyFileUpload(screen, allowedFileTypes = [], endpointURL, removeURL, requestURL)
{
    screen.find('.singleFileUpload').each(function() {
        var XHRUpload = Uppy.XHRUpload;
        var StatusBar = Uppy.StatusBar;
        var FileInput = Uppy.FileInput;
        var Informer = Uppy.Informer;

        var $singleFileUpload = $(this);
        var elementId = $singleFileUpload.attr('id');

        var $wrapper = $(this).find('.kt-uppy__wrapper');
        var $informer = $(this).find('.kt-uppy__informer');
        var $statusBar = $(this).find('.kt-uppy__status');
        var $uploadedList = $(this).find('.kt-uppy__list');
        var timeout;

        var uppyMin = Uppy.Core({
            debug: true,
            autoProceed: true,
            showProgressDetails: true,
            restrictions: {
                maxFileSize: 2097152, // 2mb
                maxNumberOfFiles: 1,
                allowedFileTypes: allowedFileTypes
            },
            locale: {
                strings: {
                    youCanOnlyUploadX: {
                        0: kjlocalization.get('algemeen', 'max_bestanden_uploaden'),
                        1: kjlocalization.get('algemeen', 'max_bestanden_uploaden')
                    },
                    exceedsSize: kjlocalization.get('algemeen', 'maximaal_toegestane_grootte_overschrijden'),
                    youCanOnlyUploadFileTypes: kjlocalization.get('algemeen', 'bestandstypefout')
                }
            }
        });

        uppyMin.use(FileInput, { target: '#' + $wrapper.attr('id'), pretty: false });
        uppyMin.use(Informer, { target: '#' + $informer.attr('id')  });

        // demo file upload server
        uppyMin.use(XHRUpload, {
            endpoint: endpointURL,
            fieldName: 'file',
            formData: true,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        uppyMin.use(StatusBar, {
            target: '#' + $statusBar.attr('id'),
            hideUploadButton: true,
            hideAfterFinish: false
        });

        $wrapper.find('.uppy-FileInput-input').addClass('kt-uppy__input-control').attr('id', elementId + '_input_control');
        $wrapper.find('.uppy-FileInput-container').append('<label class="kt-uppy__input-label btn btn-label-brand btn-bold btn-font-sm" for="' + (elementId + '_input_control') + '">'+kjlocalization.get('algemeen', 'upload_bestand')+'</label>');

        var $fileLabel = $wrapper.find('.kt-uppy__input-label');

        uppyMin.on('file-added', (file) => {
            uppyMin.setFileMeta(file.id, {
                id: $singleFileUpload.data('id'),
            })
        });

        uppyMin.on('upload', function(data) {
            $fileLabel.text("Uploading...");
            $statusBar.addClass('kt-uppy__status--ongoing');
            $statusBar.removeClass('kt-uppy__status--hidden');
            clearTimeout( timeout );
        });

        uppyMin.on('complete', function(file) {
            $.each(file.successful, function(index, value){
                var uploadListHtml = '<div class="kt-uppy__list-item" data-id="'+value.response.body.document.ID+'"><div class="kt-uppy__list-label"><a href="javascript:;" class="requestDocuments" data-id="'+value.response.body.document.ID+'">'+value.name+'</a></div><span class="kt-uppy__list-remove" data-id="'+value.response.body.document.ID+'" data-uppy-id="'+value.id+'"><i class="flaticon2-cancel-music"></i></span></div>';
                $uploadedList.append(uploadListHtml);
            });

            $wrapper.hide();

            $statusBar.addClass('kt-uppy__status--hidden');
            $statusBar.removeClass('kt-uppy__status--ongoing');
        });

        $('#' + elementId + ' .kt-uppy__list .kt-uppy__list-remove').off('click').on('click', function(e) {
            var documentId = $(this).data('id');
            var uppyId = $(this).data('uppy-id');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: removeURL + documentId,
                type: 'DELETE',
                contentType: false,
                processData: false,

                success: function (data) {
                    if (data.success === true) {
                        // Remove from uppy file list
                        if (uppyId != undefined) {
                            uppyMin.removeFile(uppyId);
                        }

                        // Remove element
                        $('#' + elementId + ' .kt-uppy__list-item[data-id="'+documentId+'"').remove();

                        // Show attach file button
                        $fileLabel.text(kjlocalization.get('algemeen', 'upload_bestand'));
                        $wrapper.show();
                    }
                }
            });
        });
    });

    screen.find('.requestDocuments').off('click').on('click', function(e) {
        e.preventDefault();

        var id = $(this).data('id');

        var formdata = new FormData();
        formdata.append('id', id);
        formdata.append('uploader_table', $('input[name=requester_table]').val());
        formdata.append('uploader_item', $('input[name=requester_item]').val());

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: requestURL,
            type: 'POST',
            contentType: false,
            processData: false,
            data: formdata,

            success: function (data) {

                if (data.success === true) {
                    var method = 'GET';
                    var url = '/document/download?token=' + data.request_token;

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
                }
            }
        });
    });
}

$('body').on('submit', 'form', function(e) {
    var saveBtn = $(this).find('.kj_save');
    if (saveBtn.length > 0) {
        saveBtn.click();
        return false;
    }
});

$('body').on('reset', 'form', function(e) {
    var form = $(this).closest('form');
    if (form.length) {
        // Reset validation
        form.validate().resetForm();
        form.find('.is-invalid').removeClass('is-invalid');

        // Executes after the form has been reset
        setTimeout(function() {
            // Refresh select pickers
            form.find('select').selectpicker('refresh');

            // Set material active labels
            setMaterialActiveLabels(form);
        }, 1);
    }
});

$(document).ready(function() {
    $('.readedNotification').on('click', function (e) {
        e.preventDefault();

        var id = $(this).data('id');
        var url = $(this).data('url');

        var formData = new FormData();
        formData.append('id', id);

        kjrequest('POST', '/admin/notification', formData, true, function (result) {
            if (result.success) {
                window.location.href = url;
            }
        });
    });
});