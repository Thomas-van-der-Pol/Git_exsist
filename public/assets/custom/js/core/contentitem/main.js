$(document).ready(function() {
    kjlocalization.create('Administration - Content', [
        {'Delete chapter title': 'Are you sure?'},
        {'Delete chapter text': 'This chapter will be removed and cannot be undone. Are you sure you want to continue?'},
    ]);

    loadSortableContainers();

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

    $('body').on('click', '.deleteContentItem', function(e) {
        e.preventDefault();

        var id = $(this).data('id');

        swal.fire({
            title: kjlocalization.get('administration_-_content', 'delete_chapter_title'),
            text: kjlocalization.get('administration_-_content', 'delete_chapter_text'),
            type: 'warning',
            width: 500,
            showCancelButton: true,
            confirmButtonText: kjlocalization.get('algemeen', 'doorgaan'),
            confirmButtonColor: '#fd397a',
            cancelButtonText: kjlocalization.get('algemeen', 'annuleren')
        }).then(function(result) {
            if (result.value) {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/admin/content-item/' + id,
                    type: 'DELETE',
                    contentType: false,
                    processData: false,

                    success: function (data) {
                        if (data.success === true) {
                            // Delete chapter from view
                            $('#content-item-card-' + id).remove();
                        }
                    }
                });
            } else if (result.dismiss === 'cancel') {
                // result.dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
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
        var div = $('div.edit-content-item-' + id);

        save($(this), '/admin/content-item', null, false, null, function(data){
            if (data.success === true) {
                if (data.new === false) {
                    $('#content-item-title-' + data.id).find('span.title').html(data.item_title);
                    $('#content-item-content-' + data.id).html(data.item_content);

                    div.empty();
                    div.attr('style','display: none');

                    $('#content-item-content-' + id).show();
                }
            }
        });
    });

    $('body').on('click', '#newContentItem', function(e) {
        var formdata = new FormData();
        formdata.append('FK_TABLE', $(this).data('table'));
        formdata.append('FK_ITEM', $(this).data('item'));

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/admin/content-item/new',
            type: 'POST',
            contentType: false,
            processData: false,
            data: formdata,

            success: function (data) {
                $('#contentItems_container').html(data.viewDetail);

                loadSortableContainers();

                $('.editContentItem[data-id='+data.id+']').click();
                $('#collapseItem'+data.id).collapse('show');
            },
        });
    });
});

function loadSortableContainers()
{
    // Each content-item
    $("#contentItems").sortable({
        connectWith: "#contentItems",
        items: ".card",
        opacity: 0.8,
        handle : '.card--sortable-handle',
        coneHelperSize: true,
        placeholder: 'kt-portlet--sortable-placeholder',
        forcePlaceholderSize: true,
        tolerance: "pointer",
        helper: "clone",
        cancel: ".kt-portlet--sortable-empty", // cancel dragging if portlet is in fullscreen mode
        // revert: 250, // animation in milliseconds
        update: function(event, ui) {
            if (ui.item.prev().hasClass("kt-portlet--sortable-empty")) {
                ui.item.prev().before(ui.item);
            }

            var data = $(this).sortable('serialize');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/admin/content-item/updateSequence',
                type: 'POST',
                data: data
            });
        }
    });
}