$(document).ready(function() {
    $('#btnSave').on('click', function(e) {
        save($(this), '/profile', null, false, null, null);
    });
});