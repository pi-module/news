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
    // route name
    'news' => array(
        'name' => 'news',
        'type' => 'Module\News\Route\News',
        'options' => array(
            'route' => '/news',
            'defaults' => array(
                'module' => 'news',
                'controller' => 'index',
                'action' => 'index'
            )
        ),
    )
);