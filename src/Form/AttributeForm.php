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

class AttributeForm extends BaseForm
{
    public function __construct($name = null, $options = [])
    {
        $this->options  = $options;
        $this->module   = Pi::service('module')->current();
        $this->position = Pi::api('attribute', 'news')->attributePositionForm();
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new AttributeFilter($this->options);
        }
        return $this->filter;
    }

    public function init()
    {
        // title
        $this->add(
            [
                'name'       => 'title',
                'options'    => [
                    'label' => __('Title'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                    'required'    => true,
                ],
            ]
        );
        // name
        $this->add(
            [
                'name'       => 'name',
                'options'    => [
                    'label' => __('Name'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => __('Set name for call anywhere, a-z 0-9 allowed'),
                    'required'    => true,
                ],
            ]
        );
        // topic
        $this->add(
            [
                'name'       => 'topic',
                'type'       => 'Module\News\Form\Element\Topic',
                'options'    => [
                    'label'  => __('Topic'),
                    'module' => $this->module,
                ],
                'attributes' => [
                    'description' => __('Set allowed topics ( main topic ) to use this attribute'),
                    'required'    => true,
                    'size'        => 5,
                    'multiple'    => 1,
                ],
            ]
        );
        // status
        $this->add(
            [
                'name'       => 'status',
                'type'       => 'select',
                'options'    => [
                    'label'         => __('Status'),
                    'value_options' => [
                        1 => __('Online'),
                        0 => __('Offline'),
                    ],
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]
        );
        // position
        $this->add(
            [
                'name'       => 'position',
                'type'       => 'select',
                'options'    => [
                    'label'         => __('Set'),
                    'value_options' => $this->position,
                ],
                'attributes' => [
                    'required'    => true,
                    'description' => __('Set view position'),
                ],
            ]
        );
        // type
        /* $this->add(array(
            'name' => 'type',
            'type' => 'select',
            'options' => array(
                'label' => __('Type'),
                'value_options' => array(
                    'text' => __('Text : type text content'),
                    'link' => __('Link : add url for click'),
                    'video' => __('Video : add flv or mp4 url for play on video player'),
                    'audio' => __('Audio : add mp3 url for play on audio player'),
                    'file' => __('File : add file link for download'),
                    'currency' => __('Currency : add view price for anything'),
                    'date' => __('Date : add date for view'),
                    'number' => __('Number : add number'),
                    'select' => __('Select : add select box for choose'),
                    'checkbox' => __('Checkbox : add check box for 0 or 1'),
                ),
            ),
            'attributes' => array(
                'required' => true,
            )
        )); */
        // Check
        if ($this->options['type'] == 'select') {
            // data
            $this->add(
                [
                    'name'       => 'data',
                    'options'    => [
                        'label' => __('General data'),
                    ],
                    'attributes' => [
                        'type'        => 'textarea',
                        'rows'        => '5',
                        'cols'        => '40',
                        'description' => __('Use `|` as delimiter to separate select box elements'),
                    ],
                ]
            );
            // default
            $this->add(
                [
                    'name'       => 'default',
                    'options'    => [
                        'label' => __('Default data'),
                    ],
                    'attributes' => [
                        'type' => 'text',
                    ],
                ]
            );
        }
        // information
        $this->add(
            [
                'name'       => 'information',
                'options'    => [
                    'label' => __('Extra information'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => __('Put page URL for click by user to explain about this field'),
                ],
            ]
        );
        // icon
        $this->add(
            [
                'name'       => 'icon',
                'options'    => [
                    'label' => __('Icon'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => __('Use fontawesome.io icons, and set icon name like fa-home'),
                ],
            ]
        );
        // search
        $this->add(
            [
                'name'       => 'search',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Search'),
                ],
                'attributes' => [
                    'description' => '',
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