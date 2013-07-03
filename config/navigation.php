<?php
/**
 * News module config
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Copyright (c) Pi Engine http://www.xoopsengine.org
 * @license         http://www.xoopsengine.org/license New BSD License
 * @author          Hossein Azizabadi <azizabadi@faragostaresh.com>
 * @since           3.0
 * @package         Module\News
 * @version         $Id$
 */

return array(
    'admin' => array(
        'story' => array(
            'label' => __('Story'),
            'route' => 'admin',
            'controller' => 'story',
            'action' => 'index',
        ),
        'topic' => array(
            'label' => __('Topic'),
            'route' => 'admin',
            'controller' => 'topic',
            'action' => 'index',
        ),
        'moderator' => array(
            'label' => __('Moderator'),
            'route' => 'admin',
            'controller' => 'moderator',
            'action' => 'index',
        ),
        'spotlight' => array(
            'label' => __('Spotlight'),
            'route' => 'admin',
            'controller' => 'spotlight',
            'action' => 'index',
        ),
        'attach' => array(
            'label' => __('Attaced'),
            'route' => 'admin',
            'controller' => 'attach',
            'action' => 'index',
        ),
        'extra' => array(
            'label' => __('Extra'),
            'route' => 'admin',
            'controller' => 'extra',
            'action' => 'index',
        ),
        /* 'permission' => array(
            'label' => __('Permission'),
            'route' => 'admin',
            'controller' => 'permission',
            'action' => 'index',
        ), */
        'tools' => array(
            'label' => __('Tools'),
            'route' => 'admin',
            'controller' => 'tools',
            'action' => 'index',
        ),
    ),
);