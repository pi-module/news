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

class Attach extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $columns
        = [
            'id', 'title', 'file', 'path', 'url', 'item_table', 'item_id', 'time_create',
            'size', 'type', 'status', 'hits',
        ];
}
