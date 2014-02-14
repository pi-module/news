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
            'label'         => _t('Index'),
            'permission'    => array(
                'resource'  => 'public',
            ),
            'route'         => 'news',
            'module'        => 'news',
            'controller'    => 'index',
            'action'        => 'index',
        ),

        'topic' => array(
            'label'         => _t('Topic list'),
            'permission'    => array(
                'resource'  => 'public',
            ),
            'route'         => 'news',
            'module'        => 'news',
            'controller'    => 'topic',
            'action'        => 'list',
        ),

        'tag' => array(
            'label'         => _t('Tag list'),
            'permission'    => array(
                'resource'  => 'public',
            ),
            'route'         => 'news',
            'module'        => 'news',
            'controller'    => 'tag',
            'action'        => 'list',
        ),

        'writer' => array(
            'label'         => _t('Writer list'),
            'permission'    => array(
                'resource'  => 'writer',
            ),
            'route'         => 'news',
            'module'        => 'news',
            'controller'    => 'writer',
            'action'        => 'list',
        ),

        'archive' => array(
            'label'         => _t('Archive'),
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
            'label' => __('Story'),
            'permission' => array(
                'resource' => 'story',
            ),
            'route' => 'admin',
            'controller' => 'story',
            'action' => 'index',
        ),
        'topic' => array(
            'label' => __('Topic'),
            'permission' => array(
                'resource' => 'topic',
            ),
            'route' => 'admin',
            'controller' => 'topic',
            'action' => 'index',
        ),
        'spotlight' => array(
            'label' => __('Spotlight'),
            'permission' => array(
                'resource' => 'spotlight',
            ),
            'route' => 'admin',
            'controller' => 'spotlight',
            'action' => 'index',
        ),
        'attach' => array(
            'label' => __('Attaced'),
            'permission' => array(
                'resource' => 'attach',
            ),
            'route' => 'admin',
            'controller' => 'attach',
            'action' => 'index',
        ),
        'extra' => array(
            'label' => __('Extra'),
            'permission' => array(
                'resource' => 'extra',
            ),
            'route' => 'admin',
            'controller' => 'extra',
            'action' => 'index',
        ),
        'tools' => array(
            'label' => __('Tools'),
            'permission' => array(
                'resource' => 'tools',
            ),
            'route' => 'admin',
            'controller' => 'tools',
            'action' => 'index',
        ),
    ),
);