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

class AuthorRoute extends AbstractRegistry
{
    /** @var string Module name */
    protected $module = 'news';

    /**
     * {@inheritDoc}
     */
    protected function loadDynamic($options = [])
    {
        // Set module
        $module = Pi::service('module')->current();
        $this->module = empty($module) ? $this->module : $module;

        $return  = [];
        $where   = ['status' => 1];
        $columns = ['id', 'slug'];
        $select  = Pi::model('author', $this->module)->select()->columns($columns)->where($where);
        $rowset  = Pi::model('author', $this->module)->selectWith($select);
        foreach ($rowset as $row) {
            $return[$row->id] = $row->slug;
        }
        return $return;
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
