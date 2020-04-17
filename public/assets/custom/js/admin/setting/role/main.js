$(document).ready(function() {
    $('body').on('click', '.deleteRole', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/settings/role/' + id, null, false,
            function(result) {
                if (result.success) {
                    ADM_ROLE_TABLE_configuration.datatableSelector.reload(null, false);
                }
            }
        );
    });
});