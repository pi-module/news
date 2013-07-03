<?php
/**
 * News module routes
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

/**
 * Routes
 */
return array(
    // route name
    'news' => array(
        // section, default as 'front'
        'section' => 'front',
        'priority' => 100,
        'type' => 'Module\\News\\Route\\News',
        'options' => array(
            'prefix' => '/news',
            'defaults' => array(
                'module' => 'news',
                'controller' => 'index',
                'action' => 'index'
            )
        ),
    ),
);