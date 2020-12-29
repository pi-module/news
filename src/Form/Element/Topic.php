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

namespace Module\News\Form\Element;

use Pi;
use Laminas\Form\Element\Select;

class Topic extends Select
{
    /**
     * @return array
     */
    public function getValueOptions()
    {
        if (empty($this->valueOptions)) {
            $module  = 'news';
            $where   = ['status' => 1];
            $columns = ['id', 'pid', 'title'];
            $order   = ['display_order ASC', 'title ASC', 'id ASC'];

            // Set type
            if (isset($this->options['type']) && in_array($this->options['type'], ['event', 'blog'])) {
                $where['type'] = $this->options['type'];
            } else {
                $where['type'] = 'general';
            }

            // Get topic list
            $select = Pi::model('topic', $module)->select()->where($where)->columns($columns)->order($order);
            $rowset = Pi::model('topic', $module)->selectWith($select);
            foreach ($rowset as $row) {
                $list[$row->id] = $row->toArray();
            }
            $this->valueOptions = $this->getTree($list);
        }
        return $this->valueOptions;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $this->Attributes = [
            'size'     => 5,
            'multiple' => 1,
            'class'    => 'form-control',
        ];
        // check form size
        if (isset($this->attributes['size'])) {
            $this->Attributes['size'] = $this->attributes['size'];
        }
        // check form multiple
        if (isset($this->attributes['multiple'])) {
            $this->Attributes['multiple'] = $this->attributes['multiple'];
        }
        return $this->Attributes;
    }

    public function getTree($elements, $parentId = 0)
    {
        $branch = [];
        // Set default category options
        if ($parentId == 0) {
            if (isset($this->options['topic'])) {
                if (empty($this->options['topic'])) {
                    $branch[0] = '';
                } elseif ($this->options['topic'] != 'full') {
                    $branch = $this->options['topic'];
                }
            } else {
                $branch[0] = __('All Topics');
            }
        }
        // Set category list as tree
        foreach ($elements as $element) {
            if ($element['pid'] == $parentId) {
                $depth                  = 0;
                $branch[$element['id']] = $element['title'];
                $children               = $this->getTree($elements, $element['id']);
                if ($children) {
                    $depth++;
                    foreach ($children as $key => $value) {
                        $branch[$key] = sprintf('%s%s', str_repeat('-', $depth), $value);
                    }
                }
                unset($elements[$element['id']]);
                unset($depth);
            }
        }
        return $branch;
    }
}
