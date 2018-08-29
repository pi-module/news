<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt BSD 3-Clause License
 */

namespace Module\News\Model\Story;

use \Pi;

class RowGateway extends \Pi\Db\RowGateway\RowGateway
{
    public function save($rePopulate = true, $filter = true)
    {
        $url = Pi::url(
            Pi::service('url')->assemble(
                'news', [
                'slug' => $this->slug,
            ]
            )
        );

        Pi::service('cache')->flushCacheByUrl($url, 'news');

        return parent::save($rePopulate, $filter);
    }
}
