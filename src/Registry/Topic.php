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
 * Topic page for route
 */
class Topic extends AbstractRegistry
{
    /** @var string Module name */
    protected $module = 'news';

    /**
     * {@inheritDoc}
     */
    protected function loadDynamic($options = array())
    {
        $list = array();
        $where = array('status' => 1);
        $columns = array('id', 'slug');
        $select = Pi::model('topic', $this->module)->select()->where($where)->columns($columns);
        $rowset = Pi::model('topic', $this->module)->selectWith($select);
        foreach ($rowset as $row) {
            $item = array(
                'name' => sprintf('topic-%s', $row->id),
                'slug' => $row->slug,
            );
            $list[$row->id] = $item;
        }
        return $list;
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
