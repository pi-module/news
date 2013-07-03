<?php
/**
 * Form element story class
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
 * @subpackage      Form
 * @version         $Id$
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
            $limit = (isset($this->options['limit'])) ? $this->options['limit'] : 50;
            $columns = array('id', 'title');
            $order = array('create DESC', 'id DESC');
            $select = Pi::model('story', $this->options['module'])->select()->columns($columns)->order($order)->limit($limit);
            $rowset = Pi::model('story', $this->options['module'])->selectWith($select);
            foreach ($rowset as $row) {
                $list[$row->id] = $row->toArray();
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
        $this->Attributes = array(
            'size' => 5,
            'multiple' => 1,
        );
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