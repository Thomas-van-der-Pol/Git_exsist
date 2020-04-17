$(document).ready(function() {
    $('body').on('click', '.editContentItem', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        var div = $('div.edit-content-item-' + id);

        $.ajax({
            url: '/admin/content-item/detailRendered/' + id,
            type: 'GET',
            dataType: 'JSON',

            success: function (data) {
                $('#content-item-content-' + id).hide();
                div.html(data.viewDetail);
                div.attr('style','display: block');

                // Summernote init
                div.find('.summernote').summernote({
                    dialogsInBody: true,
                    popover: {
                        image: [],
                        link: [],
                        air: []
                    },
                    height: 250,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['insert', ['link', 'unlink']],
                        ['misc',['codeview']]
                    ]
                });
            }
        });
    });

    $('body').on('click', '#btnCancelContentItem', function(e) {
        e.preventDefault();

        var id = $(this).closest('form').find('input[name=ID]').val();
        var div = $('div.edit-content-item-' + id);

        div.empty();
        div.attr('style','display: none');

        $('#content-item-content-' + id).show();
    });

    $('body').on('click', '#btnSaveContentItem', function(e) {
        e.preventDefault();

        var form = $(this).closest('form');
        var id = form.find('input[name=ID]').val();
        var table = form.find('input[name=FK_TABLE]').val();
        var itemId = form.find('input[name=FK_ITEM]').val();
        var div = $('div.edit-content-item-' + id);

        save($(this), '/admin/content-item', null, false, null, function(data){
            if (data.success === true) {
                if (data.new === true) {
                    var formdata = new FormData();
                    formdata.append('FK_TABLE', table);
                    formdata.append('FK_ITEM', itemId);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: '/admin/content-item/retrieveMainView',
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        data: formdata,

                        success: function (data) {
                            $('#contentItems_container').html(data.viewDetail);
                        },
                    });
                } else {
                    $('#content-item-title-' + data.id).html(data.item_title);
                    $('#content-item-content-' + data.id).html(data.item_content);

                    div.empty();
                    div.attr('style','display: none');

                    $('#content-item-content-' + id).show();
                }
            }
        });
    });
});