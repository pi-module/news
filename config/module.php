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
    // Module meta
    'meta'          => array(
        'title'         => _a('News'),
        'description'   => _a('Manage News and Blog'),
        'version'       => '1.2.0',
        'license'       => 'New BSD',
        'logo'          => 'image/logo.png',
        'readme'        => 'docs/readme.txt',
        'demo'          => 'http://demo.xoopsengine.org/news',
        'icon'          => 'fa-book',
        'clonable'      => true,
    ),
    // Author information
    'author'        => array(
        'Name'          => 'Hossein Azizabadi',
        'email'         => 'azizabadi@faragostaresh.com',
        'website'       => 'http://www.xoopsengine.org',
        'credits'       => 'Pi Engine Team'
    ),
    // resource
    'resource'      => array(
        'database'      => 'database.php',
        'config'        => 'config.php',
        'permission'    => 'permission.php',
        'block'         => 'block.php',
        'page'          => 'page.php',
        'navigation'    => 'navigation.php',
        'route'         => 'route.php',
        'comment'       => 'comment.php',
    )
);