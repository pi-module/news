<?php
/**
 * News module Story class
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
 * Pi::service('api')->news(array('Extra', 'Get'));
 * Pi::service('api')->news(array('Extra', 'Set'), $extra, $story);
 * Pi::service('api')->news(array('Extra', 'Form'), $values);
 * Pi::service('api')->news(array('Extra', 'Story'), $id);
 */

class Extra extends AbstractApi
{
    /*
      * Get list of extra fields for show in forms
      */
    public function Get()
    {
        $return = array(
            'extra' => '',
            'field' => '',
        );
        $whereField = array('status' => 1);
        $columnField = array('id', 'title');
        $orderField = array('order DESC', 'id DESC');
        $select = Pi::model('field', $this->getModule())->select()->where($whereField)->columns($columnField)->order($orderField);
        $rowset = Pi::model('field', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $return['extra'][$row->id] = $row->toArray();
            $return['field'][$row->id] = $return['extra'][$row->id]['id'];
        }
        return $return;
    }

    /*
      * Save extra field datas to DB
      */
    public function Set($extra, $story)
    {
        foreach ($extra as $field) {
            // Find row
            $where = array('field' => $field['field'], 'story' => $story);
            $select = Pi::model('data', $this->getModule())->select()->where($where)->limit(1);
            $row = Pi::model('data', $this->getModule())->selectWith($select)->current();
            // create new row
            if (empty($row)) {
                $row = Pi::model('data', $this->getModule())->createRow();
                $row->field = $field['field'];
                $row->story = $story;
            }
            // Save or delete row
            if (empty($field['data'])) {
                $row->delete();
            } else {
                $row->data = $field['data'];
                $row->save();
            }
        }
        // Set Story Extra Count
        Pi::service('api')->news(array('Story', 'ExtraCount'), $story);
    }

    /*
      * Get and Set extra field data valuse to form
      */
    public function Form($values)
    {
        $where = array('story' => $values['id']);
        $select = Pi::model('data', $this->getModule())->select()->where($where);
        $rowset = Pi::model('data', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $field[$row->field] = $row->toArray();
            $values[$field[$row->field]['field']] = $field[$row->field]['data'];
        }
        return $values;
    }

    /*
      * Get all extra field data for selected story
      */
    public function Story($id)
    {
        // Get data list
        $whereData = array('story' => $id);
        $columnData = array('field', 'data');
        $select = Pi::model('data', $this->getModule())->select()->where($whereData)->columns($columnData);
        $rowset = Pi::model('data', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $data[$row->field] = $row->toArray();
        }
        // Get field list
        $field = array();
        if (!empty($data)) {
            $whereField = array('status' => 1);
            $columnField = array('id', 'title', 'image', 'type');
            $orderField = array('order ASC', 'id ASC');
            $select = Pi::model('field', $this->getModule())->select()->where($whereField)->columns($columnField)->order($orderField);
            $rowset = Pi::model('field', $this->getModule())->selectWith($select);
            foreach ($rowset as $row) {
                $field[$row->id] = $row->toArray();
                $field[$row->id]['data'] = $data[$field[$row->id]['id']]['data'];
                if ($field[$row->id]['image']) {
                    $field[$row->id]['imageurl'] = Pi::url('upload/' . $this->getModule() . '/extra/' . $field[$row->id]['image']);
                }
            }
        }
        // return
        return $field;
    }
}