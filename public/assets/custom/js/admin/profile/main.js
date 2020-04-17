$(document).ready(function() {
    $('#btnSave').on('click', function(e) {
        save($(this), '/admin/profile', null, false, null, null);
    });
});