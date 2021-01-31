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
    // Admin section
    'admin' => [
        [
            'title'      => _a('Authors'),
            'controller' => 'author',
            'permission' => 'author',
        ],
        [
            'title'      => _a('Author roles'),
            'controller' => 'role',
            'permission' => 'role',
        ],
        [
            'title'      => _a('Attribute'),
            'controller' => 'attribute',
            'permission' => 'attribute',
        ],
        [
            'label'      => _a('Attribute position'),
            'controller' => 'position',
            'permission' => 'position',
        ],
        [
            'title'      => _a('Spotlight'),
            'controller' => 'spotlight',
            'permission' => 'spotlight',
        ],
        [
            'title'      => _a('Story'),
            'controller' => 'story',
            'permission' => 'story',
        ],
        [
            'title'      => _a('Tools'),
            'controller' => 'tools',
            'permission' => 'tools',
        ],
        [
            'title'      => _a('Topic'),
            'controller' => 'topic',
            'permission' => 'topic',
        ],
        [
            'title'      => _a('Micro blog'),
            'controller' => 'microblog',
            'permission' => 'microblog',
        ],
        [
            'title'      => _a('Json output'),
            'controller' => 'json',
            'permission' => 'json',
        ],
    ],
    // Front section
    'front' => [
        [
            'title'      => _a('Index page'),
            'controller' => 'index',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'title'      => _a('Topic'),
            'controller' => 'topic',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'title'      => _a('Topic list'),
            'controller' => 'topic',
            'action'     => 'list',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'title'      => _a('Story'),
            'controller' => 'story',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'title'      => _a('Tag'),
            'controller' => 'tag',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'title'      => _a('Tag list'),
            'controller' => 'tag',
            'action'     => 'list',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'title'      => _a('Authors'),
            'controller' => 'author',
            'permission' => 'author',
            'block'      => 1,
        ],
        [
            'title'      => _a('Favourite'),
            'controller' => 'favourite',
            'permission' => 'favourite',
            'block'      => 1,
        ],
        [
            'label'      => _a('Archive'),
            'controller' => 'archive',
            'permission' => 'public',
            'block'      => 0,
        ],
        [
            'label'      => _a('Api'),
            'controller' => 'api',
            'permission' => 'public',
            'block'      => 0,
        ],
        [
            'label'      => _a('Json output'),
            'controller' => 'json',
            'permission' => 'public',
            'block'      => 0,
        ],
    ],
    // Feed section
    'feed'  => [
        [
            'title'      => _a('Feed index'),
            'controller' => 'index',
        ],

    ],
    // Api section
    'api'   => [
        [
            'title'      => _a('Story'),
            'controller' => 'story',
        ],
        [
            'title'      => _a('Topic'),
            'controller' => 'topic',
        ],
    ],
];
