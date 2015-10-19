<?php

namespace User;

use User\Controller;
use User\GitHub;
use User\Mapper;

return [
    'controllers' => [
        'aliases' => [
            'zfcuser' => Controller\UserController::class,
        ],
        'factories' => [
            Controller\UserController::class => Controller\UserControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'scn-social-auth/user/login'    =>  __DIR__ . '/../view/scn-social-auth/user/login.phtml',
            'user/helper/new-users'         =>  __DIR__ . '/../view/user/helper/new-users.phtml',
            'zfc-user/user/index'           =>  __DIR__ . '/../view/zfc-user/user/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'newUsers' => 'User\View\Helper\NewUsersFactory',
            'userOrganizations' => 'User\View\Helper\UserOrganizationsFactory',
        ],
    ],
    'service_manager' => [
        'factories' => [
            'zfcuser_user_mapper' => Mapper\UserFactory::class,
        ],
        'invokables' => [
            GitHub\LoginListener::class => GitHub\LoginListener::class,
        ],
    ],
    'doctrine' => [
        'driver' => [
            'user_entities' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    'User\Entity' => 'user_entities'
                ]
            ]
        ],
    ],
];
