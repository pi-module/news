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
use Pi\Application\Api\AbstractApi;

/*
 * Pi::api('extra', 'news')->Get();
 * Pi::api('extra', 'news')->Set($extra, $story);
 * Pi::api('extra', 'news')->setFormValues($values);
 * Pi::api('extra', 'news')->Story($id);
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
        Pi::api('story', 'news')->ExtraCount($story);
    }

    /*
      * Get and Set extra field data valuse to form
      */
    public function setFormValues($values)
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
                switch ($row->type) {
                    case 'audio':
                        $field['audio'][$row->id] = $row->toArray();
                        $field['audio'][$row->id]['data'] = isset($data[$row->id]['data']) ? $data[$row->id]['data'] : '';
                        if ($field['audio'][$row->id]['image']) {
                            $field['audio'][$row->id]['imageUrl'] = Pi::url('upload/' . $this->getModule() . '/extra/' . $field['audio'][$row->id]['image']);
                        }
                        break;

                    case 'video':
                        $field['video'][$row->id] = $row->toArray();
                        $field['video'][$row->id]['data'] = isset($data[$row->id]['data']) ? $data[$row->id]['data'] : '';
                        if ($field['video'][$row->id]['image']) {
                            $field['video'][$row->id]['imageUrl'] = Pi::url('upload/' . $this->getModule() . '/extra/' . $field['video'][$row->id]['image']);
                        }
                        break;

                    case 'file':
                        $field['file'][$row->id] = $row->toArray();
                        $field['file'][$row->id]['data'] = isset($data[$row->id]['data']) ? $data[$row->id]['data'] : '';
                        if ($field['file'][$row->id]['image']) {
                            $field['file'][$row->id]['imageUrl'] = Pi::url('upload/' . $this->getModule() . '/extra/' . $field['file'][$row->id]['image']);
                        }
                        break;    
                    
                    default:
                        $field['all'][$row->id] = $row->toArray();
                        $field['all'][$row->id]['data'] = isset($data[$row->id]['data']) ? $data[$row->id]['data'] : '';
                        if ($field['all'][$row->id]['image']) {
                            $field['all'][$row->id]['imageUrl'] = Pi::url('upload/' . $this->getModule() . '/extra/' . $field['all'][$row->id]['image']);
                        }
                        break;
                }             
            }
        }
        // return
        return $field;
    }
}