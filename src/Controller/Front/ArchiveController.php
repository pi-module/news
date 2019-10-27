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

namespace Module\News\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Zend\Db\Sql\Predicate\Expression;

class ArchiveController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Set info
        $columns = [
            'year'  => new Expression('YEAR(FROM_UNIXTIME(time_publish))'),
            'month' => new Expression('MONTH(FROM_UNIXTIME(time_publish))'),
            'count' => new Expression('count(*)'),
        ];
        $where   = ['status' => 1];
        $group   = ['year', 'month'];

        // Select
        $select = $this->getModel('story')->select()->where($where)->columns($columns)->group($group);
        $rowset = $this->getModel('story')->selectWith($select);

        // Set list
        $list = [];
        foreach ($rowset as $row) {
            $list[$row->year][$row->month] = $row->toArray();
        }

        // Set view
        $this->view()->setTemplate('archive');
        $this->view()->assign('list', $list);
        $this->view()->assign('config', $config);
    }
}