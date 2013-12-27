<?php
/**
* Pi Engine (http://pialog.org)
*
* @link http://code.pialog.org for the Pi Engine source repository
* @copyright Copyright (c) Pi Engine http://pialog.org
* @license http://pialog.org/license.txt New BSD License
*/

/**
* @author Hossein Azizabadi <azizabadi@faragostaresh.com>
*/

return array(
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