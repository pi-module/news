<?php
/**
 * News module ACL config
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit uids.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Copyright (c) Pi Engine http://www.xoopsengine.org
 * @license         http://www.xoopsengine.org/license New BSD License
 * @uid          Hossein Azizabadi <azizabadi@faragostaresh.com>
 * @since           3.0
 * @package         Module\News
 * @version         $Id$
 */

return array(
    'resources' => array(
        // Front section
        'front' => array(
            // global public
            'public'    => array(
                'module'        => 'news',
                'title'         => __('Global public resource'),
                'access'        => array(
                    'guest'     => 1,
                    'member'    => 1,
                ),
            ),
            // Moderator
            'moderator'     => array(
                'title'         => __('Moderator page'),
                'access'        => array(
                    'staff'     => 0,
                    'moderator' => 1,
                ),
            ),
        ),
        // Admin section
        'admin' => array(
            // Generic admin resource
            'admin'     => array(
                'title'         => __('Global admin permission'),
                'access'        => array(
                    'staff'     => 1,
                ),
            ),
            // Topic
            'topic'    => array(
                'title'         => __('Topic page'),
                'access'        => array(
                    'staff'     => 0,
                    'moderator' => 1,
                ),
            ),
            // Story
            'story'     => array(
                'title'         => __('Story page'),
                'access'        => array(
                    'staff'     => 0,
                    'moderator' => 1,
                ),
            ),
            // Tools
            'tools'     => array(
                'title'         => __('Tools page'),
                'access'        => array(
                    'staff'     => 0,
                    'moderator' => 1,
                ),
            ),
            // Permission
            'permission'    => array(
                'title'         => __('Permission page'),
                'access'        => array(
                    'staff'     => 0,
                    'admin'     => 1,
                ),
            ),
            // Moderator
            'moderator'     => array(
                'title'         => __('Moderator page'),
                'access'        => array(
                    'staff'     => 0,
                    'moderator' => 1,
                ),
            ),
            // Spotlight
            'spotlight'    => array(
                'title'         => __('spotlight page'),
                'access'        => array(
                    'staff'     => 0,
                    'manager'   => 1,
                ),
            ),
            // Extra
            'extra'    => array(
                'title'         => __('Extra page'),
                'access'        => array(
                    'staff'     => 0,
                    'editor'    => 1,
                ),
            ),
        ),
    ),
);