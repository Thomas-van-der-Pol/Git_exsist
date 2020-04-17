$(document).ready(function() {
    // Load screen
    loadScreen($('#default'), {
        url: '/admin/product/detailScreen',
        mode: 'read',
        afterLoad: afterLoadScreen
    });

    // Edit action
    $('body').on('click', '.setEditMode', function(e) {
        e.preventDefault();

        var target = $(this).data('target');
        setScreenMode($('#' + target), 'edit');
    });

    // Cancel action
    $('body').on('click', '#btnCancelProduct', function(e) {
        e.preventDefault();

        setScreenMode($('#default'), 'read');
    });

    $('body').on('click', '#btnSaveProduct, #btnSaveProductNew', function(e) {
        e.preventDefault();

        save($(this), '/admin/product', null, false, null, function(data) {
            // When inserted then reload
            if (data.new == true) {
                window.location = '/admin/product/detail/' + data.id;
            }

            loadScreen($('#default'), {
                url: '/admin/product/detailScreen',
                mode: 'read',
                afterLoad: afterLoadScreen
            });
        });
    });

    $('body').on('click', '#btnCancelProductNew', function(e) {
        e.preventDefault();

        window.location = '/admin/product';
    });

    // Summernote init
    $('.summernote').summernote({
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

    //Change
    $('body').on('change', '#FK_ASSORTMENT_PRODUCT_TYPE', function(e) {
        var typeSelected = $(this).val();

        $('.assortment_producttype_service').addClass('kt-hide');
        $('.assortment_producttype_service[data-id="'+typeSelected+'"]').removeClass('kt-hide');
    });

    $('body').on('click', '.activateItem', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/product/' + id, null, false,
            function(result) {
                if (result.success) {
                    loadScreen($('#default'), {
                        url: '/admin/product/detailScreen',
                        mode: 'read',
                        afterLoad: afterLoadScreen
                    });
                }
            }
        );
    });
});

function afterLoadScreen(id, screen, data) {
    if (screen === 'default') {
        loadUppyFileUpload($('#'+screen), ['.pdf', '.docx'], '/admin/product/upload', '/admin/product/deleteFile/', '/document/request');
    }
}