var daterangepicker_ranges;

$(document).ready(function() {
    daterangepicker_ranges = new Array();
    daterangepicker_ranges[kjlocalization.get('datumtijd', 'vandaag')] = [moment(), moment()];
    daterangepicker_ranges[kjlocalization.get('datumtijd', 'morgen')] = [moment().add(1, 'days'), moment().add(1, 'days')];
    daterangepicker_ranges[kjlocalization.get('datumtijd', 'gisteren')] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    daterangepicker_ranges[kjlocalization.get('datumtijd', 'laatste_7_dagen')] = [moment().subtract(6, 'days'), moment()];
    daterangepicker_ranges[kjlocalization.get('datumtijd', 'vorige_maand')] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];
    daterangepicker_ranges[kjlocalization.get('datumtijd', 'deze_maand')] = [moment().startOf('month'), moment().endOf('month')];

    loadDropdowns();
    loadDatePickers();
    loadTimePickers();
    loadDateTimePickers();
    loadKJPostcodeLookups();
    loadToggleSwitch();
    loadKJAddressLookup();

    /**
     * Checkbox groups
     */
    $('.kjcheckboxgroup_more').click(function() {
        $(this).next('.kjcheckboxgroup_extra').show();
        $(this).next('.kjcheckboxgroup_more').show();
        $(this).hide();
    });

    $('.kjcheckboxgroup_less').click(function() {
        $(this).parent('.kjcheckboxgroup_extra').hide();
        $(this).parent('.kjcheckboxgroup_extra').prev('.kjcheckboxgroup_more').show();
    });

    /**
     * Manage button (after select box)
     */
    $('body').on('click', '.kj_managebutton', function(e) {
        e.preventDefault();

        var dropdownTypeID = $(this).data('id');

        // Callback on load trigger
        $(document).trigger('kj_managebuttonOnLoad',[$('.kj_field_modal .modal-body'), dropdownTypeID]);
        $('.kj_field_modal').modal('show');

        //Callback voor sluiten beheermodal
        $('.kj_field_modal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
            $(document).trigger('kjfieldmodal_onHidden',[$('.kj_field_modal'), dropdownTypeID]);
        });
    });

});

function readSetURLSrc(input, targetelement) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $(targetelement).attr('src', e.target.result).fadeIn(50);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function loadDropdowns() {
    kjlocalization.create('Algemeen', [
        {'Geen selectie': 'Niets geselecteerd'},
        {'Selecteer alles': 'Selecteer alles'},
        {'Deselecteer alles': 'Deselecteer alles'}
    ]);

    $('.kt-bootstrap-select').each(function() {
        var container = 'div#' + $(this).attr('id') + '-dropdown-menu';

        if (!($(container).length > 0)) {
            container = false;
        }

        $(this).selectpicker({
            noneSelectedText: kjlocalization.get('algemeen', 'geen_selectie')+'..',
            selectAllText: kjlocalization.get('algemeen', 'selecteer_alles'),
            deselectAllText: kjlocalization.get('algemeen', 'deselecteer_alles'),
            liveSearchNormalize: true,
            container: container,
            mobile: self.isMobile()
        });
    });
}

function loadToggleSwitch() {
    $('body').on('click', '.setToggleSwitch', function(e) {
        setToggleSwitchStyle($(this));
    });
}

function setToggleSwitchStyle(check)
{
    var active = check.is(':checked');
    var span = check.closest('span.kt-switch');

    if (active == 1) {
        span.removeClass('kt-switch--danger');
        span.addClass('kt-switch--success');

    } else {
        span.removeClass('kt-switch--success');
        span.addClass('kt-switch--danger');
    }
}

function loadDatePickers() {
    $('.datepicker').each(function() {

        var options = {
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom',
            language: ((window.i18n.locale !== 'undefined') ? window.i18n.locale : 'nl')
        };

        var format = $(this).data('locale-format');
        if (format !== undefined) {
            options.format = format;
        }

        $(this).datepicker(options);

        $(this).closest('.input-group').on('click', '.dateselector', function(e){
            e.preventDefault();
            $(this).closest('.input-group').find('.datepicker').datepicker('show');
        });

        $(this).closest('.input-group').on('click', '.dateclear', function(e){
            e.preventDefault();
            $(this).closest('.input-group').find('.datepicker').val('');
            $(this).closest('.input-group').find('.datepicker').change(); // trigger change
        });
    });
}

function loadTimePickers() {
    $('.timepicker').timepicker({
        defaultTime: "",
        minuteStep: 5,
        showSeconds: false,
        showMeridian: false
    });

    $('.timepicker').closest('.input-group').on('click', '.timeselector', function(e){
        e.preventDefault();
        $(this).closest('.input-group').find('.timepicker').timepicker('showWidget');
    });

    $('.timepicker').closest('.input-group').on('click', '.timeclear', function(e){
        e.preventDefault();
        $(this).closest('.input-group').find('.timepicker').val('');
        $(this).closest('.input-group').find('.timepicker').change(); // trigger change
    });
}

function loadDateTimePickers() {
    $('.datetimepicker').datetimepicker({
        autoclose: true,
        // format: 'yyyy-mm-dd', via language
        todayHighlight: true,
        orientation: 'bottom',
        language: ((window.i18n.locale !== 'undefined') ? window.i18n.locale : 'nl')
    });

    $('.datetimepicker').parent('.input-group').on('click', '.datetimeselector', function(e){
        e.preventDefault();
        $(this).parent('.input-group').find('.datetimepicker').datetimepicker('show');
    });

    $('.datetimepicker').parent('.input-group').on('click', '.dateclear', function(e){
        e.preventDefault();
        $(this).parent('.input-group').find('.datetimepicker').val('');
        $(this).parent('.input-group').find('.datetimepicker').change(); // trigger change
    });
}

function loadKJPostcodeLookups() {

    $('.refreshPostcode').off('click').on('click', function(e) {
        var inputField = $(this).closest('div.form-row').find('input[type=text]').first();

        if (!inputField.length) {
            inputField = $(this).closest('div.form-group').find('input[type=text]');
        }

        if (inputField.val() === '') {
            return;
        }

        var countryVal      = '',
            houseNumber     = '',
            postcodeVal     = inputField.val().replace(/\s/g, ''),
            targetStreet    = $('input[name='+inputField.data('street')+']'),
            targetCity      = $('input[name='+inputField.data('city')+']'),
            $loader         = $('<div class="kt-spinner kt-spinner--sm kt-spinner--brand kt-spinner--right kt-spinner--input"></div>');

        targetStreet.wrap($loader);
        targetCity.wrap($loader);
        if (inputField.attr("data-country")) {
            countryVal = inputField.attr("data-country");
        }

        if (inputField.attr("data-housenumber")) {
            houseNumber = $('input[name='+inputField.data('housenumber')+']').val();
        }

        if(targetStreet || targetCity) {
            $.ajax({
                type: "GET",
                url: '/kjfield/postcodelookup',
                data: {
                    'postcode': postcodeVal,
                    'country':  countryVal,
                    'housenumber': houseNumber
                },
                success: function (data) {
                    if(data.success) {
                        targetStreet.val(data.street);
                        targetCity.val(data.city);

                        return false;
                    }
                },

                complete: function () {
                    targetStreet.unwrap($loader);
                    targetCity.unwrap($loader);

                    setMaterialActiveLabels(targetStreet.closest('.form-group'));
                    setMaterialActiveLabels(targetCity.closest('.form-group'));
                }
            });
        }
    });
}

function loadKJAddressLookup() {
    var apiKey = '', initAutocomplete = [];

    var countLookups = $('.kjaddresslookup').length;
    $('.kjaddresslookup').each(function(index, lookup) {
        var autocomplete = [], fillInAddress, componentForm = {};

        var inputField = $(this).closest('div.form-group').find('input[type=text]');

        initAutocomplete[index] = function() {
            // Create the autocomplete object, restricting the search predictions to
            // geographical location types.
            autocomplete = new google.maps.places.Autocomplete(lookup, {types: ['geocode']});

            // Avoid paying for data that you don't need by restricting the set of
            // place fields that are returned to just the address components.
            autocomplete.setFields(['address_component']);

            // When the user selects an address from the drop-down, populate the
            // address fields in the form.
            autocomplete.addListener('place_changed', fillInAddress);
        };

        fillInAddress = function() {
            // Get the place details from the autocomplete object.
            var place = autocomplete.getPlace();

            if (place.address_components == undefined) {
                return;
            }
            for (var component in componentForm) {
                document.getElementById(component).value = '';
                document.getElementById(component).disabled = false;
            }

            // Get each component of the address from the place details,
            // and then fill-in the corresponding field on the form.
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];

                var entries = Object.entries(componentForm);
                for (var c = 0; c < entries.length; c++) {

                    // Loop door onze eigen array en zet zo de juiste velden
                    if (entries[c][1][addressType]) {
                        var val = place.address_components[i][entries[c][1][addressType]];

                        if (addressType == 'country') {
                            // Opzoeken o.b.v. data
                            document.querySelector('#'+entries[c][0]+' [data-countrycode="'+val.toLowerCase()+'"]').selected = true;
                            $('#'+entries[c][0]).change();
                        } else {
                            document.getElementById(entries[c][0]).value = val;
                        }
                    }
                }
            }
        };

        if (inputField.attr("data-street")) {
            componentForm[inputField.attr("data-street")] = {route: 'long_name'};
        }
        if (inputField.attr("data-city")) {
            componentForm[inputField.attr("data-city")] = {locality: 'long_name'};
        }
        if (inputField.attr("data-zipcode")) {
            componentForm[inputField.attr("data-zipcode")] = {postal_code: 'short_name'};
        }
        if (inputField.attr("data-housenumber")) {
            componentForm[inputField.attr("data-housenumber")] = {street_number: 'short_name'};
        }
        if (inputField.attr("data-country")) {
            componentForm[inputField.attr("data-country")] = {country: 'short_name'};
        }
        if (inputField.attr("data-apikey")) {
            apiKey = inputField.attr("data-apikey");
        }

        if (index == countLookups-1) {
            initAddresslookups = function() {
                for(var i = 0; i <= countLookups - 1; i++) {
                    initAutocomplete[i]();
                }
            };

            if ((apiKey != '') ) {
                if (typeof google === 'object' && typeof google.maps === 'object') {
                    initAddresslookups();
                } else {
                    jQuery.ajax({
                        url: 'https://maps.googleapis.com/maps/api/js?key='+apiKey+'&libraries=places&callback=initAddresslookups',
                        dataType: 'script',
                        async: true
                    });
                }
            }
        }
    });
}

function loadDateRangePickers() {

    kjlocalization.create('Datumtijd', [
        {'Maandag kort': 'ma'},
        {'Dinsdag kort': 'di'},
        {'Woensdag kort': 'wo'},
        {'Donderdag kort': 'do'},
        {'Vrijdag kort': 'vr'},
        {'Zaterdag kort': 'za'},
        {'Zondag kort': 'zo'},
        {'Januari': 'Januari'},
        {'Februari': 'Februari'},
        {'Maart': 'Maart'},
        {'April': 'April'},
        {'Mei': 'Mei'},
        {'Juni': 'Juni'},
        {'Juli': 'Juli'},
        {'Augustus': 'Augustus'},
        {'September': 'September'},
        {'Oktober': 'Oktober'},
        {'November': 'November'},
        {'December': 'December'},
        {'Vandaag': 'Vandaag'},
        {'Morgen': 'Morgen'},
        {'Gisteren': 'Gisteren'},
        {'Laatste 7 dagen': 'Laatste 7 dagen'},
        {'Vorige maand': 'Vorige maand'},
        {'Deze maand': 'Deze maand'},
        {'Handmatig bereik': 'Handmatig bereik'},
        {'Van': 'Van'},
        {'Tot': 'Tot'},
        {'Toepassen': 'Toepassen'},
        {'Wissen': 'Wissen'}
    ]);

    $('.kjdaterangepicker-picker').each(function() {
        var autoUpdateInput = ($(this).data('start-date') !== undefined);
        var format = $(this).data('locale-format');
        if (format === undefined) {
            format = "DD-MM-YYYY";
        }

        $(this).daterangepicker({
                autoUpdateInput: autoUpdateInput,
                showDropdowns: true,
                buttonClasses: 'btn',
                applyClass: 'btn-brand',
                cancelClass: 'btn-secondary',
                "locale": {
                    "format": format,
                    "separator": " / ",
                    "applyLabel": kjlocalization.get('datumtijd', 'toepassen'),
                    "cancelLabel": kjlocalization.get('datumtijd', 'wissen'),
                    "fromLabel": kjlocalization.get('datumtijd', 'van'),
                    "toLabel": kjlocalization.get('datumtijd', 'tot'),
                    "customRangeLabel": kjlocalization.get('datumtijd', 'handmatig_bereik'),
                    "daysOfWeek": [
                        kjlocalization.get('datumtijd', 'zondag_kort'),
                        kjlocalization.get('datumtijd', 'maandag_kort'),
                        kjlocalization.get('datumtijd', 'dinsdag_kort'),
                        kjlocalization.get('datumtijd', 'woensdag_kort'),
                        kjlocalization.get('datumtijd', 'donderdag_kort'),
                        kjlocalization.get('datumtijd', 'vrijdag_kort'),
                        kjlocalization.get('datumtijd', 'zaterdag_kort')
                    ],
                    "monthNames": [
                        kjlocalization.get('datumtijd', 'januari'),
                        kjlocalization.get('datumtijd', 'februari'),
                        kjlocalization.get('datumtijd', 'maart'),
                        kjlocalization.get('datumtijd', 'april'),
                        kjlocalization.get('datumtijd', 'mei'),
                        kjlocalization.get('datumtijd', 'juni'),
                        kjlocalization.get('datumtijd', 'juli'),
                        kjlocalization.get('datumtijd', 'augustus'),
                        kjlocalization.get('datumtijd', 'september'),
                        kjlocalization.get('datumtijd', 'oktober'),
                        kjlocalization.get('datumtijd', 'november'),
                        kjlocalization.get('datumtijd', 'december')
                    ],
                    "firstDay": 1
                },
                ranges: daterangepicker_ranges
            }
        ).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format(format) + ' / ' + picker.endDate.format(format));
                $(this).trigger('change');
            }
        ).on('cancel.daterangepicker', function(ev, picker) {
            //do something, like clearing an input
            $(this).val('');
            $(this).trigger('change'); // Change wordt default vooraf aangeroepen i.p.v. achteraf??
        });

        $(this).closest('.input-group').on('click', '.daterangeselector', function(e){
            e.preventDefault();
            $(this).closest('.input-group').find('.kjdaterangepicker-picker').data('daterangepicker').toggle();
        });

        $(this).closest('.input-group').on('click', '.daterangeclear', function(e){
            $(this).closest('.input-group').find('.kjdaterangepicker-picker').data('daterangepicker').clickCancel();
        });
    });
}

function isMobile()
{
    return /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4));
}