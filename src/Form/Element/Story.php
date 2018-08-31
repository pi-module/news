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
use Zend\Form\Element\Select;

class Story extends Select
{
    /**
     * @return array
     */
    public function getValueOptions()
    {
        if (empty($this->valueOptions)) {
            if (isset($this->options['story'])) {
                $options = $this->options['story'];
            }
            $limit   = (isset($this->options['limit'])) ? $this->options['limit'] : 50;
            $columns = ['id', 'title'];
            $order   = ['time_create DESC', 'id DESC'];
            $select  = Pi::model('story', $this->options['module'])->select()->columns($columns)->order($order)->limit($limit);
            $rowset  = Pi::model('story', $this->options['module'])->selectWith($select);
            foreach ($rowset as $row) {
                $list[$row->id]    = $row->toArray();
                $options[$row->id] = $list[$row->id]['title'];
            }
            $this->valueOptions = $options;
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
}