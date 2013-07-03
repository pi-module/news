<?php
/**
 * News module Writer class
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Copyright (c) Pi Engine http://www.xoopsengine.org
 * @license         http://www.xoopsengine.org/license New BSD License
 * @author          Hossein Azizabadi <azizabadi@faragostaresh.com>
 * @since           3.0
 * @package         Module\News
 * @version         $Id$
 */

namespace Module\News\Api;

use Pi;
use Pi\Application\AbstractApi;

/*
 * Pi::service('api')->news(array('Writer', 'Add'), $author);
 * Pi::service('api')->news(array('Writer', 'Delete'), $author);
 * Pi::service('api')->news(array('Writer', 'Reset'), $author, $count);
 * Pi::service('api')->news(array('Writer', 'DeleteTopic'), $topic);
 */

class Writer extends AbstractApi
{
    /*
      * Add or update Writer
      */
    public function Add($author)
    {
        $row = Pi::model('writer', $this->getModule())->find($author, 'author');
        if ($row->id) {
            $row->count = $row->count + 1;
        } else {
            $row = Pi::model('writer', $this->getModule())->createRow();
            $row->author = $author;
            $row->count = 1;
        }
        $row->save();
    }

    /*
      * Delete or update Writer
      */
    public function Delete($author)
    {
        $row = Pi::model('writer', $this->getModule())->find($author, 'author');
        if ($row->count > 1) {
            $row->count = $row->count - 1;
            $row->save();
        } else {
            $row->delete();
        }
    }

    /*
      * Reset Writer
      */
    public function Reset($author, $count)
    {
        $row = Pi::model('writer', $this->getModule())->find($author, 'author');
        if ($row) {
            $row->count = $count;
        } else {
            $row = Pi::model('writer', $this->getModule())->createRow();
            $row->author = $author;
            $row->count = $count;
        }
        $row->save();
        return array('author' => $author, 'count' => $count);
    }

    /*
      * Delete Topic
      */
    public function DeleteTopic($topic)
    {
        $select = Pi::model('story', $this->getModule())->select()->columns(array('author'))->where(array('topic' => $topic));
        $rowset = Pi::model('story', $this->getModule())->selectWith($select)->toArray();
        foreach ($rowset as $row) {
            $this->Delete($row['author']);
        }
    }
}