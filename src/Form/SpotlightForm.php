<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\News\Form;

use Pi;
use Pi\Form\Form as BaseForm;

class SpotlightForm extends BaseForm
{
    public function __construct($name = null, $module = [])
    {
        $this->module = $module;
        $this->topic  = [
            -1 => __('Home Page'),
            0  => __('All Topics'),
        ];
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
        $this->add(
            [
                'name'       => 'id',
                'attributes' => [
                    'type' => 'hidden',
                ],
            ]
        );
        // story
        $this->add(
            [
                'name'       => 'story',
                'type'       => 'Module\News\Form\Element\Story',
                'options'    => [
                    'label'  => __('Story'),
                    'module' => $this->module,
                ],
                'attributes' => [
                    'description' => __('Select story for add to Spotlight'),
                    'size'        => 1,
                    'multiple'    => 0,
                ],
            ]
        );
        // topic
        $this->add(
            [
                'name'       => 'topic',
                'type'       => 'Module\News\Form\Element\Topic',
                'options'    => [
                    'label'  => __('Show in'),
                    'module' => $this->module,
                    'topic'  => $this->topic,
                ],
                'attributes' => [
                    'description' => __('Select Spotlight topic, Your Spotlight show in this topic/page'),
                    'size'        => 1,
                    'multiple'    => 0,
                ],
            ]
        );
        // time_publish
        $this->add(
            [
                'name'       => 'time_publish',
                'options'    => [
                    'label' => __('Publish date'),
                ],
                'attributes' => [
                    'type'        => 'date',
                    'value'       => date('Y-m-d'),
                    'description' => '',
                ],
            ]
        );
        // time_expire
        $this->add(
            [
                'name'       => 'time_expire',
                'options'    => [
                    'label' => __('Expire date'),
                ],
                'attributes' => [
                    'type'        => 'date',
                    'value'       => date('Y-m-d', strtotime('+1 week')),
                    'description' => '',
                ],
            ]
        );
        // status
        $this->add(
            [
                'name'    => 'status',
                'type'    => 'select',
                'options' => [
                    'label'         => __('Status'),
                    'value_options' => [
                        1 => __('Published'),
                        2 => __('Pending review'),
                        3 => __('Draft'),
                        4 => __('Private'),
                    ],
                ],
            ]
        );
        // Save
        $this->add(
            [
                'name'       => 'submit',
                'type'       => 'submit',
                'attributes' => [
                    'value' => __('Submit'),
                ],
            ]
        );
    }
}