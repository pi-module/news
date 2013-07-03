<?php
/**
 * Spotlight form
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

class SpotlightFilter extends InputFilter
{
    public function __construct()
    {
        // id
        $this->add(array(
            'name' => 'id',
            'required' => false,
        ));
        // story
        $this->add(array(
            'name' => 'story',
            'required' => true,
        ));
        // topic
        $this->add(array(
            'name' => 'topic',
            'required' => true,
        ));
        // publish
        $this->add(array(
            'name' => 'publish',
            'required' => true,
        ));
        // expire
        $this->add(array(
            'name' => 'expire',
            'required' => true,
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'required' => true,
        ));
    }
}