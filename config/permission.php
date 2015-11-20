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
    // Front section
    'front' => array(
        'public' => array(
            'title' => _a('Global public resource'),
            'access' => array(
                'guest',
                'member',
            ),
        ),
        'author' => array(
            'title' => _a('Authors'),
            'access' => array(
                'guest',
                'member',
            ),
        ),
        'favourite' => array(
            'title' => _a('Favourite'),
            'access' => array(
                'member',
            ),
        ),
    ),
    // Admin section
    'admin' => array(
        'attach' => array(
            'title' => _a('Attach'),
            'access' => array(//'admin',
            ),
        ),
        'author' => array(
            'title' => _a('Authors'),
            'access' => array(//'admin',
            ),
        ),
        'role' => array(
            'title' => _a('Author roles'),
            'access' => array(//'admin',
            ),
        ),
        'attribute' => array(
            'title' => _a('Attribute'),
            'access' => array(//'admin',
            ),
        ),
        'position' => array(
            'title' => _a('Attribute position'),
            'access' => array(//'admin',
            ),
        ),
        'spotlight' => array(
            'title' => _a('Spotlight'),
            'access' => array(//'admin',
            ),
        ),
        'story' => array(
            'title' => _a('Story'),
            'access' => array(//'admin',
            ),
        ),
        'topic' => array(
            'title' => _a('Topic'),
            'access' => array(//'admin',
            ),
        ),
        'tools' => array(
            'title' => _a('Tools'),
            'access' => array(//'admin',
            ),
        ),
        'microblog' => array(
            'title' => _a('Micro blog'),
            'access' => array(//'admin',
            ),
        ),
    ),
);