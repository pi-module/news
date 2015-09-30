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
            'title' => _a('Attach'),
            'controller' => 'attach',
            'permission' => 'attach',
        ),
        array(
            'title' => _a('Authors'),
            'controller' => 'author',
            'permission' => 'author',
        ),
        array(
            'title' => _a('Attribute'),
            'controller' => 'attribute',
            'permission' => 'attribute',
        ),
        array(
            'label' => _a('Attribute position'),
            'controller' => 'position',
            'permission' => 'position',
        ),
        array(
            'title' => _a('Spotlight'),
            'controller' => 'spotlight',
            'permission' => 'spotlight',
        ),
        array(
            'title' => _a('Story'),
            'controller' => 'story',
            'permission' => 'story',
        ),
        array(
            'title' => _a('Tools'),
            'controller' => 'tools',
            'permission' => 'tools',
        ),
        array(
            'title' => _a('Topic'),
            'controller' => 'topic',
            'permission' => 'topic',
        ),
        array(
            'title' => _a('Micro blog'),
            'controller' => 'microblog',
            'permission' => 'microblog',
        ),
    ),
    // Front section
    'front' => array(
        array(
            'title' => _a('Index page'),
            'controller' => 'index',
            'permission' => 'public',
            'block' => 1,
        ),
        array(
            'title' => _a('Topic'),
            'controller' => 'topic',
            'permission' => 'public',
            'block' => 1,
        ),
        array(
            'title' => _a('Story'),
            'controller' => 'story',
            'permission' => 'public',
            'block' => 1,
        ),
        array(
            'title' => _a('Tag'),
            'controller' => 'tag',
            'permission' => 'public',
            'block' => 1,
        ),
        array(
            'title' => _a('Topic list'),
            'controller' => 'topic',
            'action' => 'list',
            'permission' => 'public',
            'block' => 1,
        ),
        array(
            'title' => _a('Tag list'),
            'controller' => 'tag',
            'action' => 'list',
            'permission' => 'public',
            'block' => 1,
        ),
        array(
            'title' => _a('Authors'),
            'controller' => 'author',
            'permission' => 'author',
            'block' => 1,
        ),
        array(
            'title' => _a('Favourite'),
            'controller' => 'favourite',
            'permission' => 'favourite',
            'block' => 1,
        ),
    ),
);