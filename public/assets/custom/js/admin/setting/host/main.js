var Hostname = '';
var MacAddress = '';
var Printers = [];

$(document).ready(function() {
    kjcommunicator.getComputer(setComputer);
    kjcommunicator.getPrinters(setPrinters);
});

function setComputer(data) {
    Hostname = data.name;
    MacAddress = data.macAddress;
}

function setPrinters(printers) {
    Printers = printers;
}

function setPrinterDropdown(elementName) {
    var element = $('select[name='+elementName+']');
    var rememberVal = $('input[name='+elementName+'_DUMMY]').val();

    element.empty();
    $.each(Printers, function(key, value) {
        element.append($("<option></option>").attr("value", value.name).text(value.name));
    });

    if ((rememberVal || '') != '') {
        element.val(rememberVal);
    }

    element.selectpicker('refresh');
}

$(document).on('host_tableAfterLoad', function(e, detailDiv) {
    setPrinterDropdown('PRINTER_DEFAULT');
    setPrinterDropdown('PRINTER_INVOICE');

    setMaterialActiveLabels(detailDiv);
});

$(document).on('host_tableAfterNew', function(e, detailDiv) {
    detailDiv.find('input[name=HOSTNAME]').val(Hostname);
    detailDiv.find('input[name=MAC_ADDRESS]').val(MacAddress);

    setMaterialActiveLabels(detailDiv);
});