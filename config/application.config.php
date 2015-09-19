<?php

return [
    'modules' => [
        'DoctrineModule',
        'DoctrineORMModule',
        'ZF\DevelopmentMode',
        'ZfcBase',
        'ZfcUser',
        'ZfcUserDoctrineORM',
        'ScnSocialAuth',
        'ScnSocialAuthDoctrineORM',
        'EdpGithub',
        'Application',
        'User',
        'ZfModule',
    ],
    'module_listener_options' => [
        'config_glob_paths'    => [
            'config/autoload/{,*.}{global,local}.php',
        ],
        'module_paths' => [
            './module',
            './vendor',
        ],
    ],
];
