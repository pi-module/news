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
return array(
    'front' => array(
        'favourite' => array(
            'label' => _a('Favourite'),
            'permission' => array(
                'resource' => 'public',
            ),
            'route' => 'news',
            'module' => 'news',
            'controller' => 'favourite',
            'action' => 'index',
        ),

        'topic' => array(
            'label' => _a('Topic list'),
            'permission' => array(
                'resource' => 'public',
            ),
            'route' => 'news',
            'module' => 'news',
            'controller' => 'topic',
        ),

        'tag' => array(
            'label' => _a('Tag list'),
            'permission' => array(
                'resource' => 'public',
            ),
            'route' => 'news',
            'module' => 'news',
            'controller' => 'tag',
            'action' => 'list',
        ),

        'author' => array(
            'label' => _a('Author list'),
            'permission' => array(
                'resource' => 'author',
            ),
            'route' => 'news',
            'module' => 'news',
            'controller' => 'author',
        ),
    ),
    'admin' => array(
        'story' => array(
            'label' => _a('Story'),
            'permission' => array(
                'resource' => 'story',
            ),
            'route' => 'admin',
            'controller' => 'story',
            'action' => 'index',
        ),
        'topic' => array(
            'label' => _a('Topic'),
            'permission' => array(
                'resource' => 'topic',
            ),
            'route' => 'admin',
            'controller' => 'topic',
            'action' => 'index',
        ),
        'spotlight' => array(
            'label' => _a('Spotlight'),
            'permission' => array(
                'resource' => 'spotlight',
            ),
            'route' => 'admin',
            'controller' => 'spotlight',
            'action' => 'index',
        ),
        'attach' => array(
            'label' => _a('Attaced'),
            'permission' => array(
                'resource' => 'attach',
            ),
            'route' => 'admin',
            'controller' => 'attach',
            'action' => 'index',
        ),
        'attribute' => array(
            'label' => _a('Attribute'),
            'permission' => array(
                'resource' => 'attribute',
            ),
            'route' => 'admin',
            'controller' => 'attribute',
            'action' => 'index',
        ),
        'position' => array(
            'label' => _a('Attribute position'),
            'permission' => array(
                'resource' => 'position',
            ),
            'route' => 'admin',
            'controller' => 'position',
            'action' => 'index',
        ),
        'author' => array(
            'label' => _a('Authors'),
            'permission' => array(
                'resource' => 'author',
            ),
            'route' => 'admin',
            'controller' => 'author',
            'action' => 'index',
        ),
        'role' => array(
            'label' => _a('Author roles'),
            'permission' => array(
                'resource' => 'role',
            ),
            'route' => 'admin',
            'controller' => 'role',
            'action' => 'index',
        ),
        'microblog' => array(
            'label' => _a('Micro blog'),
            'permission' => array(
                'resource' => 'microblog',
            ),
            'route' => 'admin',
            'controller' => 'microblog',
            'action' => 'index',
        ),
        'tools' => array(
            'label' => _a('Tools'),
            'permission' => array(
                'resource' => 'tools',
            ),
            'route' => 'admin',
            'controller' => 'tools',
            'action' => 'index',
        ),
        'json' => array(
            'label' => _a('Json'),
            'permission' => array(
                'resource' => 'json',
            ),
            'route' => 'admin',
            'controller' => 'json',
            'action' => 'index',
        ),
    ),
);