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

class SpotlightForm extends BaseForm
{
    public function __construct($name = null, $module = array())
    {
        $this->module = $module;
        $this->topic = array(
            -1 => __('Home Page'),
            0 => __('All Topics'),
        );
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new SpotlightFilter;
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
        // story
        $this->add(array(
            'name' => 'story',
            'type' => 'Module\News\Form\Element\Story',
            'options' => array(
                'label' => __('Story'),
                'module' => $this->module,
            ),
            'attributes' => array(
                'description' => __('Select story for add to Spotlight'),
                'size' => 1,
                'multiple' => 0,
            ),
        ));
        // topic
        $this->add(array(
            'name' => 'topic',
            'type' => 'Module\News\Form\Element\Topic',
            'options' => array(
                'label' => __('Show in'),
                'module' => $this->module,
                'topic' => $this->topic,
            ),
            'attributes' => array(
                'description' => __('Select Spotlight topic, Your Spotlight show in this topic/page'),
                'size' => 1,
                'multiple' => 0,
            ),
        ));
        // time_publish
        $this->add(array(
            'name' => 'time_publish',
            'options' => array(
                'label' => __('time_publish date'),
            ),
            'attributes' => array(
                'type' => 'date',
                'value' => date('Y-m-d'),
                'description' => '',
            )
        ));
        // time_expire
        $this->add(array(
            'name' => 'time_expire',
            'options' => array(
                'label' => __('time_expire date'),
            ),
            'attributes' => array(
                'type' => 'date',
                'value' => date('Y-m-d', strtotime('+1 week')),
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
                    1 => __('time_published'),
                    2 => __('Pending review'),
                    3 => __('Draft'),
                    4 => __('Private'),
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