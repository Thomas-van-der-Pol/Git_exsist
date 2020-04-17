$(document).ready(function() {
    $('body').on('click', '.requestDocument', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        var downloader_table = $(this).data('downloader-table');
        var downloader_item = $(this).data('downloader-item');

        var formdata = new FormData();
        formdata.append('id', id);
        formdata.append('uploader_table', downloader_table);
        formdata.append('uploader_item', downloader_item);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/shared-documents/request',
            type: 'POST',
            contentType: false,
            processData: false,
            data: formdata,

            success: function (data) {

                if (data.success === true) {
                    var method = 'GET';
                    var url = '/shared-documents/download?token=' + data.request_token;

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
});