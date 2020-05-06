$(document).ready(function() {
    $('#btnSaveSetting').on('click', function(e) {
        e.preventDefault();

        save($(this), '/admin/settings/group', null, false, null, null);
    });
});