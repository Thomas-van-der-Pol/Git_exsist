$(document).ready(function() {
    $('#btnSave').on('click', function(e) {
        save($(this), '/admin/profile', null, false, null, null);
    });

    $('#USER_PASSWORD_NEW_CONFIRM').on('keypress', function(e) {
        if (e.which == 13) {
            $('#btnSave').click();
        }
    });
});