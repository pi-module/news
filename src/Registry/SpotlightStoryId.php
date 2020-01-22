<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 * @package         Registry
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\News\Registry;

use Pi;
use Pi\Application\Registry\AbstractRegistry;

/**
 * News list
 */
class SpotlightStoryId extends AbstractRegistry
{
    /** @var string Module name */
    protected $module = 'news';

    /**
     * {@inheritDoc}
     */
    protected function loadDynamic($options = [])
    {
        // Set module
        $this->module  = Pi::service('module')->current();

        // Get config
        $config = Pi::service('registry')->config->read($this->module);

        // Set info
        $limit   = intval($config['spotlight_registry']);
        $order   = ['time_publish DESC', 'id DESC'];
        $where   = ['status' => 1, 'time_publish < ?' => time(), 'time_expire > ?' => time()];
        $columns = ['id', 'story'];
        $ids = [];

        // Select
        $select = Pi::model('spotlight', $this->module)->select()->where($where)->columns($columns)->order($order)->limit($limit);
        $rowset = Pi::model('spotlight', $this->module)->selectWith($select);

        foreach ($rowset as $row) {
            $ids[$row->story] = $row->story;
        }

        // Check empty
        if (empty($ids)) {
            // Set info
            $where   = ['status' => 1];
            $columns = ['id'];

            // Select
            $select = Pi::model('story', $this->module)->select()->where($where)->columns($columns)->order($order)->limit($limit);
            $rowset = Pi::model('story', $this->module)->selectWith($select);
            foreach ($rowset as $row) {
                $ids[$row->id] = $row->id;
            }
        }

        // return
        return $ids;
    }

    /**
     * {@inheritDoc}
     * @param array
     */
    public function read()
    {
        $options = [];
        $result  = $this->loadData($options);

        return $result;
    }

    /**
     * {@inheritDoc}
     * @param bool $name
     */
    public function create()
    {
        $this->clear('');
        $this->read();

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function setNamespace($meta = '')
    {
        return parent::setNamespace('');
    }

    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        return $this->clear('');
    }
}
