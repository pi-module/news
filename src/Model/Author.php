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

namespace Module\News\Model;

use Pi\Application\Model\Model;

class Author extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $columns
        = [
            'id', 'title', 'slug', 'text_description', 'seo_title', 'seo_keywords', 'seo_description',
            'time_create', 'time_update', 'uid', 'hits', 'image', 'path', 'status',
        ];
}