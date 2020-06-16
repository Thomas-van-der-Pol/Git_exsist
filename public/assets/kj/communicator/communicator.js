$(document).ready(function() {
    // Vertalingen publiceren
    kjlocalization.create('ERP Communicator', [
        {'Titel communicator fout': 'Communicator niet gevonden'},
        {'Titel communicator verouderd': 'Communicator verouderd'},
        {'Bericht communicator fout': 'De KJ Communicator is niet actief. Zorg er voor dat de KJ communicator geïnstalleerd en actief is voor communicatie met uw lokaal apparaat.'},
        {'Nu downloaden': 'Nu downloaden'},
        {'Herinner mij': 'Herinner mij later'},
        {'Melding genereren': 'Een moment geduld, uw documenten worden gegenereerd'},
        {'Foutmelding printen': 'Er ging iets fout tijdens het printen van het document. Probeer het opnieuw.'}
    ]);

    // Trigger options
    $(document).trigger('communicatorSetOptions',[]);

    // Controleer of geinstalleerd
    kjcommunicator.checkInstalled();
});

var kjcommunicator = {
    terminal_server: false,

    base_uri: 'http://127.0.0.1:5050/api/', // Basis moet altijd bestaan (terminal_server of niet)
    base_installed: false,

    uri: 'http://127.0.0.1:[port_number]/api/', // Voor aanroep, indien terminal_server dan is poort anders
    port_number: 5050,
    installed: false,

    version: '2.0.0',
    username: 'kjsoftware',
    password: 'sEUA%WB!rp*q',

    setOptions: function(options) {
        kjcommunicator.terminal_server = options.terminal_server || false;
    },

    setPortNumber: function(port_number) {
        kjcommunicator.port_number = port_number;
        kjcommunicator.uri = 'http://127.0.0.1:'+port_number+'/api/';
    },

    internal_checkInstalled: function(url, callback) {
        $.ajax({
            method: "GET",
            headers: {
                "Authorization": "Basic " + btoa(kjcommunicator.username + ":" + kjcommunicator.password)
            },
            url: url + "actuator/info",

            success: function(data) {
                if (data.app.version === kjcommunicator.version) {
                    // Geïnstalleerd
                    if (callback != null) {
                        callback(data);
                    }
                } else {
                    // Wel bereikbaar, maar verkeerde versie
                    swal.fire({
                        title: kjlocalization.get('erp_communicator', 'titel_communicator_verouderd'),
                        text: kjlocalization.get('erp_communicator', 'bericht_communicator_fout'),
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: kjlocalization.get('erp_communicator', 'nu_downloaden'),
                        cancelButtonText: kjlocalization.get('erp_communicator', 'herinner_mij')
                    }).then(function(result) {
                        if (result.value) {
                            $(document).trigger('communicatorDownload',[]);
                        } else if (result.dismiss === 'cancel') {
                            // result.dismiss can be 'cancel', 'overlay',
                            // 'close', and 'timer'
                            setCookie("communicator_dismiss", true, 1);
                        }
                    });
                }
            },

            error: function(data) {
                var communicator_dismiss = getCookie("communicator_dismiss");

                // Indien communicator niet uitgesteld, dan popup
                if (communicator_dismiss !== 'true') {
                    swal.fire({
                        title: kjlocalization.get('erp_communicator', 'titel_communicator_fout'),
                        text: kjlocalization.get('erp_communicator', 'bericht_communicator_fout'),
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: kjlocalization.get('erp_communicator', 'nu_downloaden'),
                        cancelButtonText: kjlocalization.get('erp_communicator', 'herinner_mij')
                    }).then(function(result) {
                        if (result.value) {
                            $(document).trigger('communicatorDownload',[]);
                        } else if (result.dismiss === 'cancel') {
                            // result.dismiss can be 'cancel', 'overlay',
                            // 'close', and 'timer'
                            setCookie("communicator_dismiss", true, 1);
                        }
                    });
                }
            }
        });
    },

    checkInstalled: function() {
        kjcommunicator.internal_checkInstalled(kjcommunicator.base_uri, function(data) {
            if (kjcommunicator.terminal_server) {
                kjcommunicator.base_installed = true;

                // Trigger after init
                $(document).trigger('communicatorAfterBaseCheck',[]);
            } else {
                kjcommunicator.installed = true;
                kjcommunicator.setPortNumber(5050);
            }
        });
    },

    writeConfiguration: function(config) {
        $.ajax({
            method: "POST",
            headers: {
                "Authorization": "Basic " + btoa(kjcommunicator.username + ":" + kjcommunicator.password)
            },
            url: kjcommunicator.base_uri + "host/write-configuration",
            contentType: "application/json; charset=utf-8",
            datatype: "json",
            data: JSON.stringify(config)
        });
    },

    checkInstalledTS: function(port_number) {
        if (kjcommunicator.terminal_server) {
            kjcommunicator.setPortNumber(port_number);

            kjcommunicator.internal_checkInstalled(kjcommunicator.uri, function(data) {
                kjcommunicator.installed = true;
            });
        } else {
            return false;
        }
    },

    openDocument: function(url, token, title) {
        $.ajax({
            method: "POST",
            headers: {
                "Authorization": "Basic " + btoa(kjcommunicator.username + ":" + kjcommunicator.password)
            },
            url: kjcommunicator.uri + "document/open",
            contentType: "application/json; charset=utf-8",
            datatype: "json",
            data: JSON.stringify({
                url: url,
                token: token,
                title: title
            }),

            success: function() {
                // Do nothing
            },

            error: function() {
                // swal.fire({
                //     title: 'Oeps!',
                //     text: kjlocalization.get('erp_communicator', 'foutmelding_printen'),
                //     type: 'error'
                // });
            }
        });
    },

    getComputer: function(callback) {
        $.ajax({
            method: "GET",
            headers: {
                "Authorization": "Basic " + btoa(kjcommunicator.username + ":" + kjcommunicator.password)
            },
            url: kjcommunicator.uri + "host",
            datatype: "json",

            success: function(data) {
                callback(data);
            },

            error: function(data) {
                callback('');
            }
        });
    },

    getPrinters: function(callback) {
        $.ajax({
            method: "GET",
            headers: {
                "Authorization": "Basic " + btoa(kjcommunicator.username + ":" + kjcommunicator.password)
            },
            url: kjcommunicator.uri + "printer",
            datatype: "json",

            success: function(data) {
                callback(data);
            },

            error: function(data) {
                callback([]);
            }
        });
    },

    printDocument: function(printerName, url, token, copies, paperWidth, paperHeight) {
        if ((paperWidth === null) || (paperWidth === undefined)) {
            paperWidth = 0;
        }

        if ((paperHeight === null) || (paperHeight === undefined)) {
            paperHeight = 0;
        }

        $.ajax({
            method: "POST",
            headers: {
                "Authorization": "Basic " + btoa(kjcommunicator.username + ":" + kjcommunicator.password)
            },
            url: kjcommunicator.uri + "printer/print",
            contentType: "application/json; charset=utf-8",
            datatype: "json",
            async: false,
            data: JSON.stringify({
                printerName: printerName,
                url: url,
                token: token,
                copies: copies,
                paperWidth: paperWidth,
                paperHeight: paperHeight
            }),

            success: function() {
                // Do nothing
            },

            error: function() {
                swal.fire({
                    title: 'Oeps!',
                    text: kjlocalization.get('erp_communicator', 'foutmelding_printen'),
                    type: 'error'
                });
            }
        });
    },

    printZPL: function(printerName, zplCode) {
        $.ajax({
            method: "POST",
            headers: {
                "Authorization": "Basic " + btoa(kjcommunicator.username + ":" + kjcommunicator.password)
            },
            url: kjcommunicator.uri + "printer/printZPL",
            contentType: "application/json; charset=utf-8",
            datatype: "json",
            data: JSON.stringify({
                printerName: printerName,
                zplCode: zplCode
            }),

            success: function() {
                // Do nothing
            },

            error: function() {
                swal.fire({
                    title: 'Oeps!',
                    text: kjlocalization.get('erp_communicator', 'foutmelding_printen'),
                    type: 'error'
                });
            }
        });
    }
};

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setDate(date.getDate() + days);
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    document.cookie = name+'=; Max-Age=-99999999;';
}