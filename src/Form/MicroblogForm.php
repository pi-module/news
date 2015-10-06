<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\News\Form;

use Pi;
use Pi\Form\Form as BaseForm;

class MicroblogForm extends BaseForm
{
    public function __construct($name = null, $option = array())
    {
        $this->module = Pi::service('module')->current();
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new MicroblogFilter;
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
        // post
        $this->add(array(
            'name' => 'post',
            'options' => array(
                'label' => __('Post'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '10',
                'cols' => '40',
                'required' => true,
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
                'required' => true,
                'size' => 1,
                'multiple' => 0,
            ),
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
                    5 => __('Delete'),
                ),
            ),
            'attributes' => array(
                'required' => true,
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