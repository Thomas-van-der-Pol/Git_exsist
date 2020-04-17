<?php

return [
    'base_uri' => env('DOCUMENTSERVICE_URL', 'http://storage01.kj.nu:5002/api/'),
    'translation_category' => 'Documents',
    'output_folder' => env('DOCUMENTSERVICE_OUTPUT_FOLDER', 'D:\Data\Secure\..'),

    'file_request' => [
        'library' => 'KJ\Core\libraries\FileRequestUtils'
    ]
];