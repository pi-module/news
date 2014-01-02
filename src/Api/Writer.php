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
namespace Module\News\Api;

use Pi;
use Pi\Application\AbstractApi;

/*
 * Pi::api('news', 'writer')->Add($uid);
 * Pi::api('news', 'writer')->Delete($uid);
 * Pi::api('news', 'writer')->Reset($uid, $count);
 * Pi::api('news', 'writer')->DeleteTopic($topic);
 */

class Writer extends AbstractApi
{
    /*
      * Add or time_update Writer
      */
    public function Add($uid)
    {
        $row = Pi::model('writer', $this->getModule())->find($uid, 'uid');
        if ($row->id) {
            $row->count = $row->count + 1;
        } else {
            $row = Pi::model('writer', $this->getModule())->createRow();
            $row->uid = $uid;
            $row->count = 1;
        }
        $row->save();
    }

    /*
      * Delete or time_update Writer
      */
    public function Delete($uid)
    {
        $row = Pi::model('writer', $this->getModule())->find($uid, 'uid');
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
    public function Reset($uid, $count)
    {
        $row = Pi::model('writer', $this->getModule())->find($uid, 'uid');
        if ($row) {
            $row->count = $count;
        } else {
            $row = Pi::model('writer', $this->getModule())->createRow();
            $row->uid = $uid;
            $row->count = $count;
        }
        $row->save();
        return array('uid' => $uid, 'count' => $count);
    }

    /*
      * Delete Topic
      */
    public function DeleteTopic($topic)
    {
        $select = Pi::model('story', $this->getModule())->select()->columns(array('uid'))->where(array('topic' => $topic));
        $rowset = Pi::model('story', $this->getModule())->selectWith($select)->toArray();
        foreach ($rowset as $row) {
            $this->Delete($row['uid']);
        }
    }
}