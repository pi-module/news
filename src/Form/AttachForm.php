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

class AttachForm extends BaseForm
{

    public function __construct($name = null, $story)
    {
        $this->module = Pi::service('module')->current();;
        $this->story = $story;
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new AttachFilter;
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
        // story
        /* $this->add(array(
            'name' => 'story',
            'type' => 'Module\News\Form\Element\Story',
            'options' => array(
                'label' => __('Story'),
                'module' => $this->module,
                'value' => $this->story,
            ),
            'attributes' => array(
                'size' => 1,
                'multiple' => 0,
            ),

        )); */
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