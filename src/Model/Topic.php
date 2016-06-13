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

class Topic extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $columns = array(
        'id',
        'pid',
        'title',
        'slug',
        'text_summery',
        'text_description',
        'image',
        'path',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'uid',
        'time_create',
        'time_update',
        'setting',
        'status',
        'style',
        'type',
    );
}