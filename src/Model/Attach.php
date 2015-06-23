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

class Attach extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $columns = array(
        'id', 'title', 'file', 'path', 'story', 'time_create', 'size', 'type', 'status', 'hits'
    );
}