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
            'title'         => _a('Attach'),
            'controller'    => 'attach',
            'permission'    => 'attach',
        ),
        array(
            'title'         => _a('Extra'),
            'controller'    => 'extra',
            'permission'    => 'extra',
        ),
        array(
            'title'         => _a('Spotlight'),
            'controller'    => 'spotlight',
            'permission'    => 'spotlight',
        ),
        array(
            'title'         => _a('Story'),
            'controller'    => 'story',
            'permission'    => 'story',
        ),
        array(
            'title'         => _a('Tools'),
            'controller'    => 'tools',
            'permission'    => 'tools',
        ),
        array(
            'title'         => _a('Topic'),
            'controller'    => 'topic',
            'permission'    => 'topic',
        ),
    ),
    // Front section
    'front' => array(
        array(
            'title'         => _a('Index page'),
            'controller'    => 'index',
            'permission'    => 'public',
            'block'         => 1,
        ),
        array(
            'title'         => _a('Topic'),
            'controller'    => 'topic',
            'permission'    => 'public',
            'block'         => 1,
        ),
        array(
            'title'         => _a('Story'),
            'controller'    => 'story',
            'permission'    => 'public',
            'block'         => 1,
        ),
        array(
            'title'         => _a('Tag'),
            'controller'    => 'tag',
            'permission'    => 'public',
            'block'         => 1,
        ),
        array(
            'title'         => _a('Writer'),
            'controller'    => 'writer',
            'permission'    => 'writer',
            'block'         => 1,
        ),
        array(
            'title'         => _a('Archive'),
            'controller'    => 'archive',
            'permission'    => 'public',
            'block'         => 1,
        ),
        array(
            'title'         => _a('Topic list'),
            'controller'    => 'topic',
            'action'        => 'list',
            'permission'    => 'public',
            'block'         => 1,
        ),
        array(
            'title'         => _a('Tag list'),
            'controller'    => 'tag',
            'action'        => 'list',
            'permission'    => 'public',
            'block'         => 1,
        ),
    ),
);