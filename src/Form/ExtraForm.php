<?php
/**
 * Extra Form
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

class ExtraForm extends BaseForm
{
    public function __construct($name = null, $options = array())
    {
        $this->imageurl = $options['imageurl'];
        $this->removeurl = empty($options['removeurl']) ? '' : $options['removeurl'];
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new ExtraFilter;
        }
        return $this->filter;
    }

    public function init()
    {
        // id
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        // title
        $this->add(array(
            'name' => 'title',
            'options' => array(
                'label' => __('Title'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // Image
        if (isset($this->imageurl)) {
            $this->add(array(
                'name' => 'imageview',
                'options' => array(
                    'label' => __('Image'),
                ),
                'attributes' => array(
                    'type' => 'image',
                    'src' => $this->imageurl,
                    'disabled' => true,
                    'description' => '',
                )
            ));
            $this->add(array(
                'name' => 'remove',
                'options' => array(
                    'label' => __('Remove image'),
                ),
                'attributes' => array(
                    'type' => 'button',
                    'class' => 'btn btn-danger btn-small',
                    'data-toggle' => 'button',
                    'data-link' => $this->removeurl,
                )
            ));
            $this->add(array(
	            'name' => 'image',
	            'attributes' => array(
	                'type' => 'hidden',
	            ),
	         ));
        } else {
            $this->add(array(
                'name' => 'image',
                'options' => array(
                    'label' => __('Image'),
                ),
                'attributes' => array(
                    'type' => 'file',
                    'description' => '',
                )
            ));
        }
        // status
        $this->add(array(
            'name' => 'status',
            'type' => 'select',
            'options' => array(
                'label' => __('Status'),
                'value_options' => array(
                    1 => __('Online'),
                    0 => __('Offline'),
                ),
            ),
        ));
        // type
        $this->add(array(
            'name' => 'type',
            'type' => 'select',
            'options' => array(
                'label' => __('Type'),
                'value_options' => array(
                    'text' => __('text'),
                    'link' => __('link'),
                    'currency' => __('currency'),
                    'date' => __('date'),
                    'number' => __('number'),
                ),
            ),
        ));
        // search
        $this->add(array(
            'name' => 'search',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Search'),
            ),
            'attributes' => array(
                'description' => '',
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