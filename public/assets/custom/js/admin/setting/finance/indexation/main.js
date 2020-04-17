$(document).ready(function() {
    var update = 0;
    retrieveIndex(update);

    $('.indexFinance').on('click', function (e) {
        e.preventDefault();

        var update = $(this).data('update');
        retrieveIndex(update);
    });
});

function retrieveIndex(update) {
    var formData = new FormData();
    formData.append('update', update);

    $.ajax({
        url: '/admin/settings/finance/indexation/configure/retrieveIndex',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,

        success: function(data) {
            if(update === 0) {
                $('#items').html(data.viewDetail);
            } else {
                window.location = '/admin/settings/finance';
            }
        }
    });
}