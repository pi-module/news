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
 * Author list
 */
class AuthorList extends AbstractRegistry
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

        // Get config
        $config = Pi::service('registry')->config->read($this->module);

        // Get author list
        $where   = ['status' => 1];
        $order   = ['title DESC', 'id DESC'];
        $columns = ['id', 'title', 'slug', 'image', 'path'];
        $author  = [];

        // Select
        $select = Pi::model('author', $this->module)->select()->where($where)->columns($columns)->order($order);
        $rowset = Pi::model('author', $this->module)->selectWith($select);

        // Make list
        foreach ($rowset as $row) {
            $author['author'][$row->id]        = $row->toArray();
            $author['author'][$row->id]['url'] = Pi::service('url')->assemble(
                'news', [
                    'module'     => $this->module,
                    'controller' => 'author',
                    'slug'       => $author['author'][$row->id]['slug'],
                ]
            );
            if ($row->image) {
                $author['author'][$row->id]['thumbUrl'] = Pi::url(
                    sprintf(
                        'upload/%s/thumb/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    )
                );
            } else {
                $author['author'][$row->id]['thumbUrl'] = Pi::url('static/avatar/local/large.png');
            }
        }

        // Get role list
        $where  = ['status' => 1];
        $order  = ['title DESC', 'id DESC'];
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
