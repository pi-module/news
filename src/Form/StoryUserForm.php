<?php
/**
 * Story user form
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
use Pi\Form\Form as BaseForm;

class StoryUserForm extends BaseForm
{
    public function __construct($name = null, $module, $options = array())
    {
        $this->module = $module;
        $this->field = $options['field'];
        $this->imageurl = $options['imageurl'];
        $this->removeurl = empty($options['removeurl']) ? '' : $options['removeurl'];
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new StoryUserFilter;
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
                'class' => 'span12',
            )
        ));
        // subtitle
        $this->add(array(
            'name' => 'subtitle',
            'options' => array(
                'label' => __('Subtitle'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
                'class' => 'span12',
            )
        ));
        // slug
        $this->add(array(
            'name' => 'slug',
            'options' => array(
                'label' => __('slug'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
                'class' => 'span12',
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
        ));
        // short		  
        $this->add(array(
            'name' => 'short',
            'options' => array(
                'label' => __('Short'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '5',
                'cols' => '40',
                'description' => '',
                'class' => 'span12',
            )
        ));
        // body	  
        $this->add(array(
            'name' => 'body',
            'options' => array(
                'label' => __('Text'),
                'editor' => 'html',
                'set' => '',
            ),
            'attributes' => array(
                'type' => 'editor',
                'description' => '',
            )
        ));
        // tag
        if (Pi::service('module')->isActive('tag')) {
            $this->add(array(
                'name' => 'tag',
                'options' => array(
                    'label' => __('Tags'),
                ),
                'attributes' => array(
                    'type' => 'text',
                    'description' => '',
                )
            ));
        }
        // important
        $this->add(array(
            'name' => 'important',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Important'),
            ),
            'attributes' => array(
                'description' => '',
            )
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'type' => 'select',
            'options' => array(
                'label' => __('Status'),
                'value_options' => array(
                    1 => __('Published'),
                    2 => __('Pending review'),
                    3 => __('Draft'),
                    4 => __('Private'),
                ),
            ),
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
                    'height' => '200',
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
        // Set extra field
        if (!empty($this->field)) {
            foreach ($this->field as $field) {
                $this->add(array(
                    'name' => $field['id'],
                    'options' => array(
                        'label' => $field['title'],
                    ),
                    'attributes' => array(
                        'type' => 'text',
                    )
                ));
            }
        }
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