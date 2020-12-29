<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return [
    'news' => [
        'title'    => _a('News comments'),
        'icon'     => 'icon-post',
        'callback' => 'Module\News\Api\Comment',
        'locator'  => 'Module\News\Api\Comment',
    ],
];
