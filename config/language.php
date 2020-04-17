<?php
/**
 * Language config
 */
return [
    'defaultLang'   => 'NL',
    'defaultLangID' => 2,
    'langs' => [
        [
            'ID'        => 2,
            'SEQUENCE'  => 1,
            'DEFAULT'   => true,
            'CODE'      => 'NL',
            'LOCALE'    => 'nl_NL',
            'ICON'      => 'nl.png',
            'ICONPATH'  => 'assets/theme/img/flags/netherlands-3@2x.svg',
            'DESCRIPTION' => 'Nederlands',
            'DATE_FORMAT'       => 'd-m-Y',
	        'DATE_FORMAT_JS'    => 'DD-MM-YYYY',
            'DATE_FORMAT_JS_DATEPICKER' => 'dd-mm-yyyy',
            'DATETIME_FORMAT'   => 'd-m-Y H:i',
            'DECIMAL_POINT'         => ',',
            'THOUSANDS_SEPARATOR'   => '.'
        ]
//        [
//            'ID'        => 3,
//            'SEQUENCE'  => 2,
//            'DEFAULT'   => false,
//            'CODE'      => 'EN',
//            'LOCALE'    => 'en_US',
//            'ICON'      => 'en.png',
//            'ICONPATH'  => 'assets/theme/img/flags/uk@2x.svg',
//            'DESCRIPTION' => 'Engels',
//            'DATE_FORMAT'       => 'd/m/Y',
//            'DATE_FORMAT_JS'    => 'DD/MM/YYYY',
//            'DATETIME_FORMAT'   => 'd/m/Y H:i',
//            'DECIMAL_POINT'         => '.',
//            'THOUSANDS_SEPARATOR'   => ','
//        ],
//        [
//            'ID'        => 4,
//            'SEQUENCE'  => 3,
//            'DEFAULT'   => false,
//            'CODE'      => 'FR',
//            'LOCALE'    => 'fr_FR',
//            'ICON'      => 'fr.png',
//            'ICONPATH'  => 'assets/theme/img/flags/France-3@2x.svg',
//            'DESCRIPTION' => 'France',
//            'DATE_FORMAT'       => 'd-m-Y',
//            'DATE_FORMAT_JS'    => 'DD-MM-YYYY',
//            'DATETIME_FORMAT'   => 'd-m-Y H:i',
//            'DECIMAL_POINT'         => ',',
//            'THOUSANDS_SEPARATOR'   => '.'
//        ]
    ]
];