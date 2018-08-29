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

class StoryAdditionalForm extends BaseForm
{
    public function __construct($name = null, $option = [])
    {
        $this->option   = $option;
        $this->field    = $option['field'];
        $this->position = Pi::api('attribute', 'news')->attributePositionForm();
        $this->module   = Pi::service('module')->current();
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new StoryAdditionalFilter($this->option);
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
        // Set attribute field
        if (!empty($this->field)) {
            foreach ($this->position as $key => $value) {
                if (!empty($this->field[$key])) {
                    // Set fieldset
                    $this->add(
                        [
                            'name'    => 'extra_position_' . $key,
                            'type'    => 'fieldset',
                            'options' => [
                                'label' => $value,
                            ],
                        ]
                    );
                    // Set list of attributes
                    foreach ($this->field[$key] as $field) {
                        if ($field['type'] == 'select') {
                            $this->add(
                                [
                                    'name'    => $field['id'],
                                    'type'    => 'select',
                                    'options' => [
                                        'label'         => $field['title'],
                                        'value_options' => $this->makeArray($field['value']),
                                    ],
                                ]
                            );
                        } elseif ($field['type'] == 'checkbox') {
                            $this->add(
                                [
                                    'name'       => $field['id'],
                                    'type'       => 'checkbox',
                                    'options'    => [
                                        'label' => $field['title'],
                                    ],
                                    'attributes' => [],
                                ]
                            );
                        } else {
                            $this->add(
                                [
                                    'name'       => $field['id'],
                                    'options'    => [
                                        'label' => $field['title'],
                                    ],
                                    'attributes' => [
                                        'type' => 'text',
                                    ],
                                ]
                            );
                        }
                    }
                }
            }
        }
        // Save
        $this->add(
            [
                'name'       => 'submit',
                'type'       => 'submit',
                'attributes' => [
                    'value' => __('Submit'),
                    'class' => 'btn btn-primary',
                ],
            ]
        );
    }

    public function makeArray($values)
    {
        $list     = [];
        $values   = json_decode($values, true);
        $variable = explode('|', $values['data']);
        foreach ($variable as $value) {
            $list[$value] = $value;
        }
        return $list;
    }
}