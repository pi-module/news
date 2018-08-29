<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return [
    // Front section
    'front' => [
        'public'    => [
            'title'  => _a('Global public resource'),
            'access' => [
                'guest',
                'member',
            ],
        ],
        'author'    => [
            'title'  => _a('Authors'),
            'access' => [
                'guest',
                'member',
            ],
        ],
        'favourite' => [
            'title'  => _a('Favourite'),
            'access' => [
                'member',
            ],
        ],
    ],
    // Admin section
    'admin' => [
        'author'    => [
            'title'  => _a('Authors'),
            'access' => [//'admin',
            ],
        ],
        'role'      => [
            'title'  => _a('Author roles'),
            'access' => [//'admin',
            ],
        ],
        'attribute' => [
            'title'  => _a('Attribute'),
            'access' => [//'admin',
            ],
        ],
        'position'  => [
            'title'  => _a('Attribute position'),
            'access' => [//'admin',
            ],
        ],
        'spotlight' => [
            'title'  => _a('Spotlight'),
            'access' => [//'admin',
            ],
        ],
        'story'     => [
            'title'  => _a('Story'),
            'access' => [//'admin',
            ],
        ],
        'topic'     => [
            'title'  => _a('Topic'),
            'access' => [//'admin',
            ],
        ],
        'tools'     => [
            'title'  => _a('Tools'),
            'access' => [//'admin',
            ],
        ],
        'microblog' => [
            'title'  => _a('Micro blog'),
            'access' => [//'admin',
            ],
        ],
        'json'      => [
            'title'  => _a('Json'),
            'access' => [//'admin',
            ],
        ],
    ],
];