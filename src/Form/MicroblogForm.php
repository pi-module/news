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

class MicroblogForm extends BaseForm
{
    public function __construct($name = null, $option = [])
    {
        $this->module = Pi::service('module')->current();
        $this->option = $option;
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
        $this->add(
            [
                'name'       => 'id',
                'attributes' => [
                    'type' => 'hidden',
                ],
            ]
        );
        // post
        $this->add(
            [
                'name'       => 'post',
                'options'    => [
                    'label' => __('Post'),
                ],
                'attributes' => [
                    'type'     => 'textarea',
                    'rows'     => '10',
                    'cols'     => '40',
                    'required' => true,
                ],
            ]
        );
        // topic
        if ($this->option == 'post') {
            $this->add(
                [
                    'name'       => 'topic',
                    'attributes' => [
                        'type'  => 'hidden',
                        'value' => 0,
                    ],
                ]
            );
        } elseif ($this->option == 'news') {
            $this->add(
                [
                    'name'       => 'topic',
                    'attributes' => [
                        'type'  => 'hidden',
                        'value' => 1,
                    ],
                ]
            );
        } else {
            $this->add(
                [
                    'name'       => 'topic',
                    'attributes' => [
                        'type' => 'hidden',
                    ],
                ]
            );
        }
        // status
        $this->add(
            [
                'name'       => 'status',
                'type'       => 'select',
                'options'    => [
                    'label'         => __('Status'),
                    'value_options' => [
                        1 => __('Published'),
                        2 => __('Pending review'),
                        3 => __('Draft'),
                        4 => __('Private'),
                        5 => __('Delete'),
                    ],
                ],
                'attributes' => [
                    'required' => true,
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
