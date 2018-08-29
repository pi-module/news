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

class Story extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $rowClass = 'Module\News\Model\Story\RowGateway';

    /**
     * {@inheritDoc}
     */
    protected $columns
        = [
            'id',
            'title',
            'subtitle',
            'slug',
            'topic',
            'topic_main',
            'author',
            'text_summary',
            'text_description',
            'text_html',
            'seo_title',
            'seo_keywords',
            'seo_description',
            'important',
            'status',
            'time_create',
            'time_update',
            'time_publish',
            'uid',
            'hits',
            'image',
            'path',
            'cropping',
            'main_image',
            'additional_images',
            'point',
            'count',
            'favourite',
            'attach',
            'attribute',
            'type',
        ];

    protected $mediaLinks = ['main_image', 'additional_images'];
}