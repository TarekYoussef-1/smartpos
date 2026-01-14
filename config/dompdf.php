<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Show warnings
    |--------------------------------------------------------------------------
    */
    'show_warnings' => false,

    /*
    |--------------------------------------------------------------------------
    | Public path
    |--------------------------------------------------------------------------
    */
    'public_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Convert entities
    |--------------------------------------------------------------------------
    */
    'convert_entities' => true,

    /*
    |--------------------------------------------------------------------------
    | DOMPDF Options
    |--------------------------------------------------------------------------
    */
    'options' => [
        'temp_dir' => sys_get_temp_dir(),
        'chroot' => realpath(base_path()),
        'log_output_file' => null,
        'enable_php' => false,
        'enable_javascript' => false,
        'enable_remote' => false,
        'enable_html5_parser' => true,
        'pdf_backend' => 'CPDF',
        'dpi' => 96,
        'font_height_ratio' => 1.1,
        'default_paper_size' => 'custom',
        'default_paper_orientation' => 'portrait',
    ],

    /*
    |--------------------------------------------------------------------------
    | Font Configuration (الأهم)
    |--------------------------------------------------------------------------
    */
    'font_dir' => storage_path('fonts/'),
    'font_cache' => storage_path('fonts/cache/'),

    'custom_font_dir' => storage_path('fonts/'),
    'custom_font_data' => [

        'amiri' => [
            'R' => 'Amiri-Regular.ttf',
            'B' => 'Amiri-Bold.ttf',
        ],

    ],

    'default_font' => 'amiri',
];
