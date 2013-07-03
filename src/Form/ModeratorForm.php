<?php
/**
 * Moderator form
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

namespace Module\News\Form;

use Pi;
use Pi\Form\Form as BaseForm;

class ModeratorForm extends BaseForm
{
    protected $options;

    public function __construct($name = null, $module)
    {
        $this->module = $module;
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new ModeratorFilter;
        }
        return $this->filter;
    }

    public function init()
    {


        // manager
        $this->add(array(
            'name' => 'manager',
            'options' => array(
                'label' => __('Manager'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => 'Please enter manager user id at here',
            )
        ));
        // topic
        $this->add(array(
            'name' => 'topic',
            'type' => 'Module\News\Form\Element\Topic',
            'options' => array(
                'label' => __('Topic'),
                'module' => $this->module,
                'topic' => '',
            ),
            'attributes' => array(
                'description' => __('Please select a topic for add moderator'),
                'size' => 1,
                'multiple' => 0,
            ),
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Status'),
            ),
            'attributes' => array(
                'description' => 'You can set manager status',
            )
        ));
        // Save
        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'value' => __('Submit'),
            )
        ));
    }
}	