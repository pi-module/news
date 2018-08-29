<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return [
    'front' => [
        'favourite' => [
            'label'      => _a('Favourite'),
            'permission' => [
                'resource' => 'public',
            ],
            'route'      => 'news',
            'module'     => 'news',
            'controller' => 'favourite',
            'action'     => 'index',
        ],

        'topic' => [
            'label'      => _a('Topic list'),
            'permission' => [
                'resource' => 'public',
            ],
            'route'      => 'news',
            'module'     => 'news',
            'controller' => 'topic',
        ],

        'tag' => [
            'label'      => _a('Tag list'),
            'permission' => [
                'resource' => 'public',
            ],
            'route'      => 'news',
            'module'     => 'news',
            'controller' => 'tag',
            'action'     => 'list',
        ],

        'author' => [
            'label'      => _a('Author list'),
            'permission' => [
                'resource' => 'author',
            ],
            'route'      => 'news',
            'module'     => 'news',
            'controller' => 'author',
        ],
    ],
    'admin' => [
        'story'     => [
            'label'      => _a('Story'),
            'permission' => [
                'resource' => 'story',
            ],
            'route'      => 'admin',
            'controller' => 'story',
            'action'     => 'index',
        ],
        'topic'     => [
            'label'      => _a('Topic'),
            'permission' => [
                'resource' => 'topic',
            ],
            'route'      => 'admin',
            'controller' => 'topic',
            'action'     => 'index',
        ],
        'spotlight' => [
            'label'      => _a('Spotlight'),
            'permission' => [
                'resource' => 'spotlight',
            ],
            'route'      => 'admin',
            'controller' => 'spotlight',
            'action'     => 'index',
        ],
        'attribute' => [
            'label'      => _a('Attribute'),
            'permission' => [
                'resource' => 'attribute',
            ],
            'route'      => 'admin',
            'controller' => 'attribute',
            'action'     => 'index',
            'pages'      => [
                'attribute' => [
                    'label'      => _a('Attribute'),
                    'permission' => [
                        'resource' => 'attribute',
                    ],
                    'route'      => 'admin',
                    'controller' => 'attribute',
                    'action'     => 'index',
                ],
                'position'  => [
                    'label'      => _a('Attribute position'),
                    'permission' => [
                        'resource' => 'position',
                    ],
                    'route'      => 'admin',
                    'controller' => 'position',
                    'action'     => 'index',
                ],
            ],
        ],
        'author'    => [
            'label'      => _a('Authors'),
            'permission' => [
                'resource' => 'author',
            ],
            'route'      => 'admin',
            'controller' => 'author',
            'action'     => 'index',
            'pages'      => [
                'author' => [
                    'label'      => _a('Authors'),
                    'permission' => [
                        'resource' => 'author',
                    ],
                    'route'      => 'admin',
                    'controller' => 'author',
                    'action'     => 'index',
                ],
                'role'   => [
                    'label'      => _a('Author roles'),
                    'permission' => [
                        'resource' => 'role',
                    ],
                    'route'      => 'admin',
                    'controller' => 'role',
                    'action'     => 'index',
                ],
            ],
        ],
        'microblog' => [
            'label'      => _a('Micro blog'),
            'permission' => [
                'resource' => 'microblog',
            ],
            'route'      => 'admin',
            'controller' => 'microblog',
            'action'     => 'index',
        ],
        'tools'     => [
            'label'      => _a('Tools'),
            'permission' => [
                'resource' => 'tools',
            ],
            'route'      => 'admin',
            'controller' => 'tools',
            'action'     => 'index',
            'pages'      => [
                'tools'     => [
                    'label'      => _a('Tools'),
                    'permission' => [
                        'resource' => 'tools',
                    ],
                    'route'      => 'admin',
                    'controller' => 'tools',
                    'action'     => 'index',
                ],
                'rebuild'   => [
                    'label'      => _a('Rebuild'),
                    'permission' => [
                        'resource' => 'tools',
                    ],
                    'route'      => 'admin',
                    'controller' => 'tools',
                    'action'     => 'rebuild',
                ],
                'prune'     => [
                    'label'      => _a('Prune'),
                    'permission' => [
                        'resource' => 'tools',
                    ],
                    'route'      => 'admin',
                    'controller' => 'tools',
                    'action'     => 'prune',
                ],
                'spotlight' => [
                    'label'      => _a('Spotlight'),
                    'permission' => [
                        'resource' => 'tools',
                    ],
                    'route'      => 'admin',
                    'controller' => 'tools',
                    'action'     => 'spotlight',
                ],
                'page'      => [
                    'label'      => _a('Page'),
                    'permission' => [
                        'resource' => 'tools',
                    ],
                    'route'      => 'admin',
                    'controller' => 'tools',
                    'action'     => 'page',
                ],
                'sitemap'   => [
                    'label'      => _a('Sitemap'),
                    'permission' => [
                        'resource' => 'tools',
                    ],
                    'route'      => 'admin',
                    'controller' => 'tools',
                    'action'     => 'sitemap',
                ],
                'media'     => [
                    'label'      => _a('Media'),
                    'permission' => [
                        'resource' => 'tools',
                    ],
                    'route'      => 'admin',
                    'controller' => 'tools',
                    'action'     => 'media',
                ],
                'json'      => [
                    'label'      => _a('Json'),
                    'permission' => [
                        'resource' => 'json',
                    ],
                    'route'      => 'admin',
                    'controller' => 'json',
                    'action'     => 'index',
                ],
            ],
        ],
    ],
];