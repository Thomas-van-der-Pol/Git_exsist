$(document).ready(function() {
    $('.kj_datatable').each(function() {
        var swipeShowed = $('meta[name="kj_swipe_showed"]').attr('content');

        if (swipeShowed == 0) {
            $(this).append(
                '<div class="kj_datatable--overlay">' +
                '   <img align="center" src="assets/custom/img/pages/swipe-left.png" alt="Swipe" title="Swipe" height="50"/>' +
                '</div>'
            );

            setTimeout(function() {
                $('.kj_datatable--overlay').remove();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/profile/swipeShowed',
                    type: 'POST',
                    contentType: false,
                    processData: false
                });
            }, 1500)
        }
    });
});