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
    'front'   => array(
        'index' => array(
            'label'         => _a('Index'),
            'permission'    => array(
                'resource'  => 'public',
            ),
            'route'         => 'news',
            'module'        => 'news',
            'controller'    => 'index',
            'action'        => 'index',
        ),

        'topic' => array(
            'label'         => _a('Topic list'),
            'permission'    => array(
                'resource'  => 'public',
            ),
            'route'         => 'news',
            'module'        => 'news',
            'controller'    => 'topic',
            'action'        => 'list',
        ),

        'tag' => array(
            'label'         => _a('Tag list'),
            'permission'    => array(
                'resource'  => 'public',
            ),
            'route'         => 'news',
            'module'        => 'news',
            'controller'    => 'tag',
            'action'        => 'list',
        ),

        'writer' => array(
            'label'         => _a('Writer list'),
            'permission'    => array(
                'resource'  => 'writer',
            ),
            'route'         => 'news',
            'module'        => 'news',
            'controller'    => 'writer',
            'action'        => 'list',
        ),

        'author' => array(
            'label'         => _a('Author list'),
            'permission'    => array(
                'resource'  => 'author',
            ),
            'route'         => 'news',
            'module'        => 'news',
            'controller'    => 'author',
            'action'        => 'list',
        ),

        'archive' => array(
            'label'         => _a('Archive'),
            'permission'    => array(
                'resource'  => 'public',
            ),
            'route'         => 'news',
            'module'        => 'news',
            'controller'    => 'archive',
            'action'        => 'index',
        ),
    ),
    'admin' => array(
        'story' => array(
            'label'         => _a('Story'),
            'permission'    => array(
                'resource'  => 'story',
            ),
            'route'         => 'admin',
            'controller'    => 'story',
            'action'        => 'index',
        ),
        'topic' => array(
            'label'         => _a('Topic'),
            'permission'    => array(
                'resource'  => 'topic',
            ),
            'route'         => 'admin',
            'controller'    => 'topic',
            'action'        => 'index',
        ),
        'spotlight' => array(
            'label'         => _a('Spotlight'),
            'permission'    => array(
                'resource'  => 'spotlight',
            ),
            'route'         => 'admin',
            'controller'    => 'spotlight',
            'action'        => 'index',
        ),
        'attach' => array(
            'label'         => _a('Attaced'),
            'permission'    => array(
                'resource'  => 'attach',
            ),
            'route'         => 'admin',
            'controller'    => 'attach',
            'action'        => 'index',
        ),
        'extra' => array(
            'label'         => _a('Extra'),
            'permission'    => array(
                'resource'  => 'extra',
            ),
            'route'         => 'admin',
            'controller'    => 'extra',
            'action'        => 'index',
        ),
        'author' => array(
            'label'         => _a('Authors'),
            'permission'    => array(
                'resource'  => 'author',
            ),
            'route'         => 'admin',
            'controller'    => 'author',
            'action'        => 'index',
        ),
        'tools' => array(
            'label'         => _a('Tools'),
            'permission'    => array(
                'resource'  => 'tools',
            ),
            'route'         => 'admin',
            'controller'    => 'tools',
            'action'        => 'index',
        ),
    ),
);