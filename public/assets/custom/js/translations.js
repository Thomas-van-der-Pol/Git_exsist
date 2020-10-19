$(document).ready(function () {
    kjlocalization.create('Algemeen', [
        {'Max bestanden uploaden': 'U kunt alleen %{smart_count} bestand uploaden'},
        {'Maximaal toegestane grootte overschrijden': 'Dit bestand overschrijdt de maximaal toegestane grootte van'},
        {'Bestandstypefout': 'Je kunt alleen %{types} uploaden'},
        {'Upload bestand': 'Upload bestand'},
        {'Actief': 'Actief'},
        {'Inactief': 'Inactief'},
        {'Doorgaan': 'Doorgaan'},
        {'Annuleren': 'Annuleren'},
        {'Inklappen': 'Inklappen'},
        {'Uitklappen': 'Uitklappen'},
        {'Verwijderen': 'Verwijderen'},
        {'Kopieren': 'Kopieren'},
        {'Openen': 'Openen'},
        {'Ja': 'Ja'},
        {'Nee': 'Nee'},
        {'Anonimiseren titel': 'Weet je het zeker?'},
        {'Anonimiseren text': 'Weet je zeker dat je door wilt gaan?'},
        {'Selecteer minimaal een regel': 'Selecteer minimaal een regel om door te gaan'},
        {'Geen selectie': 'Niets geselecteerd'},
        {'Selecteer alles': 'Selecteer alles'},
        {'Deselecteer alles': 'Deselecteer alles'},
        {'Succesvol': 'Opgeslagen!'},
        {'Foutmelding': 'Er is een fout opgetreden!'},
        {'Email validatie': 'Gelieve een geldig e-mailadres in te geven'},
        {'Succesvol gepubliceerd': 'Succesvol gepubliceerd! Ververs de pagina om de wijzigingen in te zien.'},
    ]);

    kjlocalization.create('Admin - CRM', [
        {'Opslaan + taak': 'Opslaan + taak'},
        {'Emailadres leeg': 'Emailadres leeg'},
        {'Wachtwoord succesvol verzonden': 'Wachtwoord succesvol verzonden'},
        {'Selecteer relatie': 'Selecteer relatie'},
        {'Selecteer contactpersoon': 'Selecteer contactpersoon'}
    ]);

    kjlocalization.create('Admin - Dossiers', [
        {'Selecteer product': 'Selecteer product'},
        {'Selecteer dienst': 'Selecteer dienst'},
        {'Verwijder projectproduct titel': 'Weet je het zeker?'},
        {'Verwijder projectproduct tekst': 'Alle taken en alle factuurmomenten die aan deze interventie gekoppeld zijn worden verwijderd.'},
        {'Dossier mist ziektedag of polisnummer': 'Dossier mist ziektedag of polisnummer'},
    ]);

    kjlocalization.create('Admin - Boekhouding', [
        {'Successvol geexporteerd': 'De gegevens zijn succesvol geëxporteerd!'},
        {'Exporteren mislukt': 'Fout bij exporteren van gegevens'},
        {'Successvol geimporteerd': 'De gegevens zijn succesvol geïmporteerd!'},
        {'Importeren mislukt': 'Fout bij importeren van gegevens'}
    ]);

    kjlocalization.create('Admin - Facturen', [
        {'Bericht status wijziging': 'Weet u zeker dat u door wilt gaan?'},
        {'Bericht versturen': 'Weet u zeker dat u deze factuur defintief wilt verzenden? Deze actie is niet ongedaan te maken.'},
        {'Bericht versturen herinnering': 'Weet u zeker dat u een herinnering van deze factuur wilt verzenden? Deze actie is niet ongedaan te maken.'},
        {'Conceptfactuur verwijderen': 'Weet u zeker dat u de conceptfactuur wilt verwijderen? Deze actie is niet ongedaan te maken.'},
        {'Bericht printen': 'Let op: de factuur wordt geprint omdat digitaal factureren niet is ingeschakeld. Wilt u doorgaan met printen?'},
        {'Geen regels geselecteerd': 'Geen regels geselecteerd'},
        {'Bericht selectie factureren': 'Weet je zeker dat je deze facturen definitief wil versturen? Dit actie is niet ongedaan te maken.'},
        {'Bericht selectie herinnering': 'Weet je zeker dat je deze facturen een herinnering wil versturen? Dit actie is niet ongedaan te maken.'},
        {'Bericht selectie printen': 'Let op: enkele facturen worden geprint omdat digitaal factureren niet is ingeschakeld. Wilt u doorgaan met printen?'},
        {'Peildatum niet gevuld': 'Peildatum niet gevuld'},
        {'Geen facturen geselecteerd': 'Geen facturen geselecteerd'}
    ]);

    kjlocalization.create('Admin - Werknemers', [
        {'Emailadres leeg': 'E-mailadres is leeg. Wachtwoord kan niet worden gereset!'},
    ]);

    kjlocalization.create('Admin - Taken', [
        {'Selecteer een werknemer': 'Selecteer een werknemer'},
        {'Map verwijderen titel': 'Weet je het zeker'},
        {'Map verwijderen tekst': 'Alle taken in deze map worden ontkoppeld van deze map.'},
        {'Nieuwe taak': 'Nieuwe taak'},
        {'Tekst meetellen factuurdatums': 'Wil je ook de factuurschema datums laten meetellen?'},
    ]);

    kjlocalization.create('Administration - Content', [
        {'Delete chapter title': 'Are you sure?'},
        {'Delete chapter text': 'This chapter will be removed and cannot be undone. Are you sure you want to continue?'},
    ]);

    kjlocalization.create('Documenten', [
        {'Document verwijderen titel': 'Weet je het zeker?'},
        {'Document verwijderen tekst': 'Dit document wordt verwijderd en kan niet ongedaan worden gemaakt. Weet je zeker dat je door wilt gaan?'},
        {'Naam': 'Naam'},
        {'Grootte': 'Grootte'},
        {'Type': 'Type'},
        {'Gewijzigd op': 'Gewijzigd op'},
        {'Home': 'Home'},
        {'Selecteer minimaal een regel': 'Selecteer minimaal een regel'},
        {'Voer mapnaam in': 'Voer mapnaam in'},
        {'Mapnaam mag niet leeg zijn': 'Mapnaam mag niet leeg zijn'},
        {'Voer naam in': 'Voer naam in'},
        {'Naam mag niet leeg zijn': 'Naam mag niet leeg zijn'},
        {'Deze map is leeg': 'Deze map is leeg'},
    ]);

    kjlocalization.create('ERP Communicator', [
        {'Titel communicator fout': 'Communicator niet gevonden'},
        {'Titel communicator verouderd': 'Communicator verouderd'},
        {'Bericht communicator fout': 'De KJ Communicator is niet actief. Zorg er voor dat de KJ communicator geïnstalleerd en actief is voor communicatie met uw lokaal apparaat.'},
        {'Nu downloaden': 'Nu downloaden'},
        {'Herinner mij': 'Herinner mij later'},
        {'Melding genereren': 'Een moment geduld, uw documenten worden gegenereerd'},
        {'Foutmelding printen': 'Er ging iets fout tijdens het printen van het document. Probeer het opnieuw.'}
    ]);

    kjlocalization.create('Tabellen', [
        {'Verwerkmelding': 'Een moment geduld...'},
        {'Geen resultaten': 'Geen resultaten gevonden'},
        {'Eerste pagina': 'Eerste'},
        {'Vorige pagina': 'Vorige'},
        {'Volgende pagina': 'Volgende'},
        {'Laatste pagina': 'Laatste'},
        {'Meer paginas': 'Meer paginas'},
        {'Pagina nummer': 'Pagina nummer'},
        {'Selecteer pagina grootte': 'Selecteer pagina grootte'},
        {'Aantal resultaten': 'Weergeven {{start}} - {{end}} van {{total}} resultaten'},
        {'Huidig geselecteerde regels': 'Huidig geselecteerde regels'}
    ]);

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
});