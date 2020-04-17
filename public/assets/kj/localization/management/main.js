$(document).ready(function() {
    $('#publishTranslations').on('click', function(e) {
        e.preventDefault();

        var btn = $(this);

        KTApp.progress(btn);
        startKJLoader({funVersion:true,CrazyLoader:true});

        kjlocalization.create('Algemeen', [
            {'Succesvol gepubliceerd': 'Succesvol gepubliceerd! Ververs de pagina om de wijzigingen in te zien.'},
            {'Foutmelding': 'Er ging iets mis:'}
        ]);

        $.ajax({
            url: window.i18n.publishTranslationUrl,
            type: 'GET',
            success: function (data) {
                var message = kjlocalization.get('algemeen', 'succesvol_gepubliceerd');
                $.notify({message:message},{type: 'success'});

                stopKJLoader();
                KTApp.unprogress(btn);
            },
            error: function(data) {
                var message = kjlocalization.get('algemeen', 'foutmelding');
                $.notify({message:message + ' ' + data},{type: 'danger'});

                stopKJLoader();
                KTApp.unprogress(btn);
            }
        });
    });
});

$(document).on('translation_tableAfterLoad', function(e, detailDiv) {
    var keyTable = detailDiv.find('.kj_datatable').attr('id');
    var eventName = keyTable + 'AfterLoad';

    $(document).off(eventName).on(eventName, function(e, detailDiv) {
        //Summernote initen
        detailDiv.find('.summernote').summernote({
            dialogsInBody: true,
            popover: {
                image: [],
                link: [],
                air: []
            },
            height: 100,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['insert', ['link', 'unlink']],
                ['misc',['codeview']]
            ]
        });
    });

    // Filters tonen
    var doKeypress = false;
    if ($('input[name=translationSearch]').val() !== '') {
        doKeypress = true;
    }

    $('input[name=translationKeySearch]').val($('input[name=translationSearch]').val());

    $('.keyFilters').show();

    if (doKeypress === true) {
        var event = jQuery.Event('keypress');
        event.which = 13;

        $('input[name=translationKeySearch]').trigger(event);
    }
});