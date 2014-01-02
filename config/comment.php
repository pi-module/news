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
    'news' => array(
        'title'    => _a('News comments'),
        'icon'     => 'icon-post',
        'callback' => 'Module\News\Api\Comment',
        'locator'  => 'Module\News\Api\Comment',
    ),
);