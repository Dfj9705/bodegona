<?php

return [
    'name' => 'Estilo Americano',
    'manifest' => [
        'name' => env('APP_NAME', 'Estilo Americano'),
        'short_name' => 'Estilo Americano GT',
        'start_url' => '/',
        'background_color' => '#ffffff',
        'theme_color' => '#E5533D',
        'display' => 'standalone',
        'orientation' => 'any',
        'status_bar' => 'black',
        'icons' => [
            '72x72' => [
                'path' => '/images/icons/ea_logo_72x72.png',
                'purpose' => 'any'
            ],
            '96x96' => [
                'path' => '/images/icons/ea_logo_96x96.png',
                'purpose' => 'any'
            ],
            '128x128' => [
                'path' => '/images/icons/ea_logo_128x128.png',
                'purpose' => 'any'
            ],
            '144x144' => [
                'path' => '/images/icons/ea_logo_144x144.png',
                'purpose' => 'any'
            ],
            '152x152' => [
                'path' => '/images/icons/ea_logo_152x152.png',
                'purpose' => 'any'
            ],
            '192x192' => [
                'path' => '/images/icons/ea_logo_192x192.png',
                'purpose' => 'any'
            ],
            '384x384' => [
                'path' => '/images/icons/ea_logo_384x384.png',
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => '/images/icons/ea_logo_512x512.png',
                'purpose' => 'any'
            ],
        ],
        'splash' => [
            '640x1136' => '/images/icons/splash-640x1136.png',
            '750x1334' => '/images/icons/splash-750x1334.png',
            '828x1792' => '/images/icons/splash-828x1792.png',
            '1125x2436' => '/images/icons/splash-1125x2436.png',
            '1242x2208' => '/images/icons/splash-1242x2208.png',
            '1242x2688' => '/images/icons/splash-1242x2688.png',
            '1536x2048' => '/images/icons/splash-1536x2048.png',
            '1668x2224' => '/images/icons/splash-1668x2224.png',
            '1668x2388' => '/images/icons/splash-1668x2388.png',
            '2048x2732' => '/images/icons/splash-2048x2732.png',
        ],
        'shortcuts' => [
            [
                'name' => 'Movimientos',
                'description' => 'Ingreso de movimientos',
                'url' => '/movement',
                'icons' => [
                    "src" => "/images/icons/ea_logo_72x72.png",
                    "purpose" => "Ingreso de movimientos"
                ]
            ],
            [
                'name' => 'Productos',
                'description' => 'Creación de productos',
                'url' => '/product',
                'icons' => [
                    "src" => "/images/icons/ea_logo_72x72.png",
                    "purpose" => "Creación de productos"
                ]
            ],
        ],
        'custom' => []
    ]
];
