<?php

return [
    'propel' => [
        'database' => [
            'connections' => [
                'notizverwaltung' => [
                    'adapter'    => 'mysql',
                    'classname'  => 'Propel\Runtime\Connection\ConnectionWrapper',
                    'dsn'        => 'mysql:host=localhost;dbname=notizverwaltung',
                    'user'       => 'root',
                    'password'   => 'password',
                    'attributes' => []
                ]
            ]
        ],
        'runtime' => [
            'defaultConnection' => 'notizverwaltung',
            'connections' => ['notizverwaltung']
        ],
        'generator' => [
            'defaultConnection' => 'notizverwaltung',
            'connections' => ['notizverwaltung']
        ]
    ]
];