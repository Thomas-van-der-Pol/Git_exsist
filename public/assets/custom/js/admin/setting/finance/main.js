$(document).ready(function() {
    $('body').on('click', '.deleteIndexation', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        kjrequest('DELETE', '/admin/settings/finance/indexation/' + id, null, false,
            function(result) {
                if (result.success) {
                    ADM_FINANCE_INDEXATION_TABLE_configuration.datatableSelector.reload(null, false);
                }
            }
        );
    });
});