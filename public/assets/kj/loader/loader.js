$(document).ready(function() {
    var kjLoaderCrazy = null;
});

function startKJLoader(options) {
    // Override options GLOBAAL uitzetten voor je applicatie
    if(kjloaderGlobalOptions.funVersionDisabled) {
        options.funVersion = undefined;
    }

    // Override options GLOBAAL uitzetten voor je applicatie
    if(kjloaderGlobalOptions.crazyLoaderDisabled) {
        options.CrazyLoader = undefined;
    }

        //Afwijkende titel
    (typeof options.customTitle !== 'undefined') ? ( $('.kj_loader_modal_title').html(options.customTitle) ) : $('.kj_loader_modal_title').html($('.kj_loader_modal_title').data('default'));

    //Content
    (typeof options.customContent !== 'undefined') ? ( $('.kj_loader_modal_content').html(options.customContent).show() ) : $('.kj_loader_modal_content').html('').hide();

    //Gif/Mp4 inladen
    if(typeof options.funVersion !== 'undefined') {
        // // Zet de image
        var videoArray = [];
        if (kjloaderGlobalOptions.funVersionSets != null) {
            videoArray = kjloaderGlobalOptions.funVersionSets['default'];
        }

        if(typeof options.funVersionSet !== 'undefined') {
            videoArray = kjloaderGlobalOptions.funVersionSets[options.funVersionSet]
        }

        var rand = Math.floor(Math.random() * videoArray.length);
        var video = '<video muted loop style="max-height: 430px; width: 100%;" webkit-playsinline="true" playsinline="true">';
        video += '<source src="'+videoArray[rand]+'" type="video/mp4">';
        video += '</video>';

        $('.kj_loader_modal_fun').html(video);
        // Play video bij openen zodat deze op de achtergrond niet blijt bewegen
        $('.kj_loader_modal_fun video').get(0).play();

        $('.kj_loader_modal_fun').fadeIn(500)
    } else {
        $('.kj_loader_modal_fun').hide();
    }

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
    // Pauzeer als je de loader stopt
    $('.kj_loader_modal_fun video').get(0).pause();
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