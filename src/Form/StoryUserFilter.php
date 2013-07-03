<?php
/**
 * story user form
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

namespace Module\News\Form;

use Pi;
use Zend\InputFilter\InputFilter;

class StoryUserFilter extends InputFilter
{
    public function __construct($extra = null)
    {
        // id
        $this->add(array(
            'name' => 'id',
            'required' => false,
        ));
        // title
        $this->add(array(
            'name' => 'title',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // subtitle
        $this->add(array(
            'name' => 'subtitle',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // alias
        $this->add(array(
            'name' => 'alias',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // topic
        $this->add(array(
            'name' => 'topic',
            'required' => true,
        ));
        // short
        $this->add(array(
            'name' => 'short',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // body
        $this->add(array(
            'name' => 'body',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // tag
        if (Pi::service('module')->isActive('tag')) {
            $this->add(array(
                'name' => 'tag',
                'required' => false,
            ));
        }
        // important
        $this->add(array(
            'name' => 'important',
            'required' => false,
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'required' => true,
        ));
        // image
        $this->add(array(
            'name' => 'image',
            'required' => false,
        ));
        // Set extra field
        if (!empty($extra)) {
            foreach ($extra as $field) {
                $this->add(array(
                    'name' => $field['id'],
                    'required' => false,
                ));
            }
        }
    }
}