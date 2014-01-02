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
 * Pi::api('news', 'extra')->Get();
 * Pi::api('news', 'extra')->Set($extra, $story);
 * Pi::api('news', 'extra')->Form($values);
 * Pi::api('news', 'extra')->Story($id);
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
        $orderField = array('order DESC', 'id DESC');
        $select = Pi::model('field', $this->getModule())->select()->where($whereField)->order($orderField);
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
            $select = Pi::model('field_data', $this->getModule())->select()->where($where)->limit(1);
            $row = Pi::model('field_data', $this->getModule())->selectWith($select)->current();
            // create new row
            if (empty($row)) {
                $row = Pi::model('field_data', $this->getModule())->createRow();
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
        // Set story Extra Count
        Pi::api('news', 'story')->ExtraCount($story);
    }

    /*
      * Get and Set extra field data valuse to form
      */
    public function Form($values)
    {
        $where = array('story' => $values['id']);
        $select = Pi::model('field_data', $this->getModule())->select()->where($where);
        $rowset = Pi::model('field_data', $this->getModule())->selectWith($select);
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
        $select = Pi::model('field_data', $this->getModule())->select()->where($whereData)->columns($columnData);
        $rowset = Pi::model('field_data', $this->getModule())->selectWith($select);
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
                    $field[$row->id]['imageUrl'] = Pi::url('upload/' . $this->getModule() . '/extra/' . $field[$row->id]['image']);
                }
            }
        }
        // return
        return $field;
    }
}