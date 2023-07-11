<?php
return [
    'path' => base_path() . '/app/Modules',
    'base_namespace' => 'App\Modules',
    'groupWithoutPrefix' => ['Pub','Api'],

    'groupMidleware' => [
        'Admin' => [
            'web' => ['auth'],
            'api' => ['auth:api'],
        ]
    ],

    'modules' => [
        'Admin' => [
            'Dashboard',
            'Menu',
            'User'
        ],
        'Api' => [
            'MissionNext'
        ],
        'Pub' => [
            'Auth'
        ],
    ]
];
