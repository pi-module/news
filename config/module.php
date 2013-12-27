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
    // Module meta
    'meta' => array(
        'title'         => _a('News'),
        'description'   => _a('Manage News and Blog'),
        'version'       => '1.0.4',
        'license'       => 'New BSD',
        'logo'          => 'image/logo.png',
        'readme'        => 'docs/readme.txt',
        'demo'          => 'http://demo.xoopsengine.org/news',
        'icon'          => 'fa fa fa-book',
        'clonable'      => true,
    ),
    // Author information
    'author' => array(
        'name'    => 'Hossein Azizabadi',
        'email'   => 'azizabadi@faragostaresh.com',
        'website' => 'http://www.xoopsengine.org',
        'credits' => 'Pi Engine Team'
    ),
    // resource
    'resource' => array(
        'database' => 'database.php',
        'permission'    => 'permission.php',
        'config'        => 'config.php',
        'block'         => 'block.php',
        'page'          => 'page.php',
        'route'         => 'route.php',
        'navigation'    => 'navigation.php',
    )

);