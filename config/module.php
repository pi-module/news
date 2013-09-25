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

/**
 * Application manifest
 */
return array(
    // Module meta
    'meta' => array(
        // Module title, required
        'title' => __('News'),
        // Description, for admin, optional
        'description' => __('Manage News and Blog'),
        // Version number, required
        'version' => '1.0.3',
        // Distribution license, required
        'license' => 'New BSD',
        // Logo image, for admin, optional
        'logo' => 'image/logo.png',
        // Readme file, for admin, optional
        'readme' => 'docs/readme.txt',
        // Direct download link, available for wget, optional
        //'download'      => 'http://dl.xoopsengine.org/module/news',
        // Demo site link, optional
        'demo' => 'http://demo.xoopsengine.org/news',
        // Module is ready for clone? Default as false
        'clonable' => true,
    ),
    // Author information
    'author' => array(
        // Author full name, required
        'name' => 'Hossein Azizabadi',
        // Email address, optional
        'email' => 'azizabadi@faragostaresh.com',
        // Website link, optional
        'website' => 'http://www.xoopsengine.org',
        // Credits and aknowledgement, optional
        'credits' => 'Zend Framework Team; Pi Engine Team'
    ),
    // Module dependency: list of module directory names, optional
    'dependency' => array(
    ),
    // Maintenance actions
    'maintenance' => array(
        // resource
        'resource' => array(
            // Database meta
            'database' => 'database.php',
            // Module configs
            'config' => 'config.php',
            // ACL specs
            'acl' => 'acl.php',
            // Block definition
            'block' => 'block.php',
            // Bootstrap, priority
            'bootstrap' => 1,
            // View pages
            'page' => 'page.php',
            // Navigation definition
            'navigation' => 'navigation.php',
            // Routes, first in last out; bigger priority earlier out
            'route' => 'route.php',
        )
    )
);