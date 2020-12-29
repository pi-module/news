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

namespace Module\News\Model;

use Pi\Application\Model\Model;

class Topic extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $columns
        = [
            'id',
            'pid',
            'title',
            'slug',
            'text_summary',
            'text_description',
            'image',
            'path',
            'seo_title',
            'seo_keywords',
            'seo_description',
            'uid',
            'time_create',
            'time_update',
            'display_order',
            'setting',
            'status',
            'style',
            'type',
        ];
}
