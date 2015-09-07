<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt BSD 3-Clause License
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
    protected function loadDynamic($options = array())
    {
        $ids = array();
        // Get config
        $config = Pi::service('registry')->config->read($this->module);
        // Set info
        $limit =  intval($config['spotlight_registry']);
        $order = array('time_publish DESC', 'id DESC');
        $where = array('status' => 1, 'time_publish < ?' => time(), 'time_expire > ?' => time());
        $columns = array('id', 'story');
        // Select
        $select = Pi::model('spotlight', $this->module)->select()->where($where)->columns($columns)->order($order)->limit($limit);
        $rowset = Pi::model('spotlight', $this->module)->selectWith($select);
        foreach ($rowset as $row) {
            $ids[$row->story] = $row->story;
        }
        // Check empty
        if (empty($ids)) {
            // Set info
            $where = array('status' => 1);
            $columns = array('id');
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
        $options = array();
        $result = $this->loadData($options);

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
