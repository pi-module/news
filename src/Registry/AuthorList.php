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
 * Author list
 */
class AuthorList extends AbstractRegistry
{
    /** @var string Module name */
    protected $module = 'news';

    /**
     * {@inheritDoc}
     */
    protected function loadDynamic($options = array())
    {
        $author = array();
        // Get config
        $config = Pi::service('registry')->config->read($this->module);
        // Get author list
        $where = array('status' => 1);
        $order = array('title DESC', 'id DESC');
        $columns = array('id', 'title', 'slug', 'image', 'path');
        $select = Pi::model('author', $this->module)->select()->where($where)->columns($columns)->order($order);
        $rowset = Pi::model('author', $this->module)->selectWith($select);
        foreach ($rowset as $row) {
            $author['author'][$row->id] = $row->toArray();
            $author['author'][$row->id]['url'] = Pi::service('url')->assemble('news', array(
                'module' => $this->module,
                'controller' => 'author',
                'slug' => $author['author'][$row->id]['slug'],
            ));
            if ($row->image) {
                $author['author'][$row->id]['thumbUrl'] = Pi::url(
                    sprintf('upload/%s/thumb/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    ));
            } else {
                $author['author'][$row->id]['thumbUrl'] = Pi::url('static/avatar/local/large.png');
            }
        }
        // Get role list
        $where = array('status' => 1);
        $order = array('title DESC', 'id DESC');
        $select = Pi::model('author_role', $this->module)->select()->where($where)->order($order);
        $rowset = Pi::model('author_role', $this->module)->selectWith($select);
        foreach ($rowset as $row) {
            $author['role'][$row->id] = $row->toArray();
        }
        return $author;
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
