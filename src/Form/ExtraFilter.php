<?php
/**
 * Extra form
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

class ExtraFilter extends InputFilter
{
    public function __construct()
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
        // image
        $this->add(array(
            'name' => 'image',
            'required' => false,
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'required' => true,
        ));
        // type
        $this->add(array(
            'name' => 'type',
            'required' => true,
        ));
        // search
        $this->add(array(
            'name' => 'search',
            'required' => false,
        ));
    }
}