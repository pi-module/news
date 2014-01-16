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
    // Admin section
    'admin' => array(
        array(
            'controller'    => 'attach',
            'permission'    => 'attach',
        ),
        array(
            'controller'    => 'extra',
            'permission'    => 'extra',
        ),
        array(
            'controller'    => 'spotlight',
            'permission'    => 'spotlight',
        ),
        array(
            'controller'    => 'story',
            'permission'    => 'story',
        ),
        array(
            'controller'    => 'tools',
            'permission'    => 'tools',
        ),
        array(
            'controller'    => 'topic',
            'permission'    => 'topic',
        ),
    ),
    // Front section
    'front' => array(
        array(
            'controller'    => 'index',
            'permission'    => 'index',
            'block'         => 1,
        ),
        array(
            'controller'    => 'topic',
            'permission'    => 'topic',
            'block'         => 1,
        ),
        array(
            'controller'    => 'story',
            'permission'    => 'story',
            'block'         => 1,
        ),
        array(
            'controller'    => 'tag',
            'permission'    => 'tag',
            'block'         => 1,
        ),
        array(
            'controller'    => 'writer',
            'permission'    => 'writer',
            'block'         => 1,
        ),
        array(
            'controller'    => 'archive',
            'permission'    => 'archive',
            'block'         => 1,
        ),
    ),
);