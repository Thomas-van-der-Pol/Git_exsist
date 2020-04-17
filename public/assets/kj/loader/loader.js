$(document).ready(function() {
    var kjLoaderCrazy = null;
});

function startKJLoader(options) {

    //Afwijkende titel
    (typeof options.customTitle !== 'undefined') ? ( $('.kj_loader_modal_title').html(options.customTitle) ) : $('.kj_loader_modal_title').html($('.kj_loader_modal_title').data('default'));

    //Content
    (typeof options.customContent !== 'undefined') ? ( $('.kj_loader_modal_content').html(options.customContent).show() ) : $('.kj_loader_modal_content').html('').hide();

    //Gif
    (typeof options.funVersion !== 'undefined') ? ( $('.kj_loader_modal_fun').fadeIn(500) ) : $('.kj_loader_modal_fun').hide();

	// Default voor als geen crazyloader
	kjLoaderCrazy = false;
	
    //Weergeven met prevent close
    $('.kj_loader_modal').modal({
        backdrop: 'static',
        keyboard: false
    }).show();

    //Options
    optGoCrazy = (typeof options.CrazyLoader !== 'undefined') ? options.CrazyLoader : false;
    if(optGoCrazy) {
        goCrazyKJLoader();
    }
}

function stopKJLoader() {
    $('.kj_loader_modal').modal('hide');
    stopRealyCrazy();

    //Fix if not loaded
    setTimeout(function() {
        $('.kj_loader_modal').modal('hide');
        stopRealyCrazy();
    }, 500);
}

function stopRealyCrazy() {
    if(kjLoaderCrazy) {
        clearInterval(kjLoaderCrazy);
    }
}

function goCrazyKJLoader() {
    function goRealyCrazy() {
        var Rand = Math.floor(Math.random() * (99 - 1 + 1)) + 1;
        $('.progress-bar').css('width', Rand+'%').attr('aria-valuenow', Rand);
    }
    kjLoaderCrazy = setInterval(goRealyCrazy, 1000);
}