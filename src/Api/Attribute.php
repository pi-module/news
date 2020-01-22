<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\News\Api;

use Pi;
use Pi\Application\Api\AbstractApi;

/*
 * Pi::api('attribute', 'news')->Get($topic);
 * Pi::api('attribute', 'news')->Set($attribute, $story);
 * Pi::api('attribute', 'news')->setFormValues($values);
 * Pi::api('attribute', 'news')->Story($id);
 * Pi::api('attribute', 'news')->SearchForm($form);
 * Pi::api('attribute', 'news')->findFromattribute($search);
 * Pi::api('attribute', 'news')->setTopic($field, $topicArr);
 * Pi::api('attribute', 'news')->getTopic($field);
 * Pi::api('attribute', 'news')->getField($business);
 * Pi::api('attribute', 'news')->attributePositionForm();
 */

class Attribute extends AbstractApi
{
    public function __construct()
    {
        $this->module = Pi::service('module')->current();
    }

    /*
      * Get list of attribute fields for show in forms
      */
    public function Get($topic = '')
    {
        // Set return
        $return = [
            'attribute' => [],
            'field'     => [],
        ];
        // Get position list
        $position = $this->attributePositionForm();
        // Get field id from business
        $id = $this->getField($topic);
        if (empty($id)) {
            return $return;
        }
        // find
        $whereField = ['status' => 1, 'id' => $id];
        $orderField = ['order ASC', 'position ASC', 'id DESC'];
        $select     = Pi::model('field', $this->getModule())->select()->where($whereField)->order($orderField);
        $rowset     = Pi::model('field', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $return['attribute'][$row->position][$row->id] = $row->toArray();
            switch ($row->type) {
                case 'text':
                    $type_vew = __('Text');
                    break;

                case 'link':
                    $type_vew = __('Link');
                    break;

                case 'video':
                    $type_vew = __('Video');
                    break;

                case 'audio':
                    $type_vew = __('Audio');
                    break;

                case 'file':
                    $type_vew = __('File');
                    break;

                case 'currency':
                    $type_vew = __('Currency');
                    break;

                case 'date':
                    $type_vew = __('Date');
                    break;

                case 'number':
                    $type_vew = __('Number');
                    break;

                case 'select':
                    $type_vew = __('Select');
                    break;

                case 'checkbox':
                    $type_vew = __('Checkbox');
                    break;
            }
            $return['attribute'][$row->position][$row->id]['type_vew']     = $type_vew;
            $return['attribute'][$row->position][$row->id]['position_vew'] = $position[$row->position];
            $return['field'][$row->id]                                     = $return['attribute'][$row->position][$row->id]['id'];
        }
        return $return;
    }

    /*
      * Save attribute field datas to DB
      */
    public function Set($attribute, $story)
    {
        foreach ($attribute as $field) {
            // Find row
            $where  = ['field' => $field['field'], 'story' => $story];
            $select = Pi::model('field_data', $this->getModule())->select()->where($where)->limit(1);
            $row    = Pi::model('field_data', $this->getModule())->selectWith($select)->current();
            // create new row
            if (empty($row)) {
                $row        = Pi::model('field_data', $this->getModule())->createRow();
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
        // Set story attribute Count
        Pi::api('story', 'news')->attributeCount($story);
    }

    /*
      * Get and Set attribute field data valuse to form
      */
    public function Form($values)
    {
        $where  = ['story' => $values['id']];
        $select = Pi::model('field_data', $this->getModule())->select()->where($where);
        $rowset = Pi::model('field_data', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $field[$row->field]                   = $row->toArray();
            $values[$field[$row->field]['field']] = $field[$row->field]['data'];
        }
        return $values;
    }

    /*
      * Get all attribute field data for selected story
      */
    public function Story($id, $topic)
    {
        $position = $this->attributePositionForm();
        // Get data list
        $whereData  = ['story' => $id];
        $columnData = ['field', 'data'];
        $select     = Pi::model('field_data', $this->getModule())->select()->where($whereData)->columns($columnData);
        $rowset     = Pi::model('field_data', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $data[$row->field] = $row->toArray();
        }
        // Get field list
        $field = [];
        if (!empty($data)) {
            // Get field id from topic
            $id = $this->getField($topic);
            if (empty($id)) {
                return [];
            }
            // Select
            $whereField = ['status' => 1, 'id' => $id];
            $orderField = ['order ASC', 'id ASC'];
            $select     = Pi::model('field', $this->getModule())->select()->where($whereField)->order($orderField);
            $rowset     = Pi::model('field', $this->getModule())->selectWith($select);
            foreach ($rowset as $row) {
                switch ($row->type) {
                    case 'audio':
                        $field['audio'][$row->id]         = $row->toArray();
                        $field['audio'][$row->id]['data'] = isset($data[$row->id]['data']) ? $data[$row->id]['data'] : '';
                        break;

                    case 'video':
                        $field['video'][$row->id]         = $row->toArray();
                        $field['video'][$row->id]['data'] = isset($data[$row->id]['data']) ? $data[$row->id]['data'] : '';
                        break;

                    default:
                        $field['all'][$row->position]['info'][$row->id]         = $row->toArray();
                        $field['all'][$row->position]['info'][$row->id]['data'] = isset($data[$row->id]['data']) ? $data[$row->id]['data'] : '';
                        $field['all'][$row->position]['title']                  = $position[$row->position];
                        break;
                }
            }
        }
        // return
        return $field;
    }

    /*
      * Set attribute filds from search form
      */
    public function SearchForm($form)
    {
        $attribute = [];
        // unset other field
        unset($form['type']);
        unset($form['title']);
        unset($form['price_from']);
        unset($form['price_to']);
        unset($form['topic']);
        // Make list
        foreach ($form as $key => $value) {
            if (is_numeric($key) && !empty($value)) {
                $item            = [];
                $item['field']   = $key;
                $item['data']    = $value;
                $attribute[$key] = $item;
            }
        }
        return $attribute;
    }

    /*
      * Set attribute filds from search form
      */
    public function findFromAttribute($search)
    {
        $id     = [];
        $column = ['story'];
        foreach ($search as $attribute) {
            $where  = [
                'field' => $attribute['field'],
                'data'  => $attribute['data'],
            ];
            $select = Pi::model('field_data', $this->getModule())->select()->where($where)->columns($column);
            $rowset = Pi::model('field_data', $this->getModule())->selectWith($select);
            foreach ($rowset as $row) {
                if (isset($row->story) && !empty($row->story)) {
                    $id[] = $row->story;
                }
            }
        }
        $id = array_unique($id);
        return $id;
    }

    public function attributePositionForm()
    {
        // Get info
        $list   = [
            '' => '',
            0  => __('Hidden'),
        ];
        $where  = [
            'status' => 1,
        ];
        $order  = ['order ASC', 'id ASC'];
        $select = Pi::model('field_position', $this->getModule())->select()->where($where)->order($order);
        $rowset = Pi::model('field_position', $this->getModule())->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = $row->title;
        }
        return $list;
    }

    public function setTopic($field, $topicArr)
    {
        // Remove
        Pi::model('field_topic', $this->getModule())->delete(['field' => $field]);
        // Add
        foreach ($topicArr as $topic) {
            // Save
            $row        = Pi::model('field_topic', $this->getModule())->createRow();
            $row->field = $field;
            $row->topic = $topic;
            $row->save();
        }
    }

    public function getTopic($field)
    {
        $topic  = [];
        $where  = ['field' => $field];
        $select = Pi::model('field_topic', $this->getModule())->select()->where($where);
        $rowset = Pi::model('field_topic', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $topic[] = $row->topic;
        }
        return array_unique($topic);
    }

    public function getField($topic = '')
    {
        $field = [];
        if (!empty($topic)) {
            $where = ['topic' => [$topic, 0]];
        } else {
            $where = ['topic' => 0];
        }
        $select = Pi::model('field_topic', $this->getModule())->select()->where($where);
        $rowset = Pi::model('field_topic', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $field[] = $row->field;
        }
        return array_unique($field);
    }
}