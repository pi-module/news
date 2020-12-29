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

class AuthorForm extends BaseForm
{
    public function __construct($name = null, $option = [])
    {
        $this->option    = $option;
        $this->thumbUrl  = (isset($options['thumbUrl'])) ? $options['thumbUrl'] : '';
        $this->removeUrl = empty($options['removeUrl']) ? '' : $options['removeUrl'];
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new AuthorFilter($this->option);
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
                ],
            ]
        );
        // slug
        $this->add(
            [
                'name'       => 'slug',
                'options'    => [
                    'label' => __('slug'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                ],
            ]
        );
        // text_description
        $this->add(
            [
                'name'       => 'text_description',
                'options'    => [
                    'label'  => __('Description'),
                    'editor' => 'html',
                    'set'    => '',
                ],
                'attributes' => [
                    'type'        => 'editor',
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
        // Image
        if ($this->thumbUrl) {
            $this->add(
                [
                    'name'       => 'imageview',
                    'type'       => 'Module\News\Form\Element\Image',
                    'options'    => [//'label' => __('Image'),
                    ],
                    'attributes' => [
                        'src' => $this->thumbUrl,
                    ],
                ]
            );
            $this->add(
                [
                    'name'       => 'remove',
                    'type'       => 'Module\News\Form\Element\Remove',
                    'options'    => [
                        'label' => __('Remove image'),
                    ],
                    'attributes' => [
                        'link' => $this->removeUrl,
                    ],
                ]
            );
            $this->add(
                [
                    'name'       => 'image',
                    'attributes' => [
                        'type' => 'hidden',
                    ],
                ]
            );
        } else {
            $this->add(
                [
                    'name'       => 'image',
                    'options'    => [
                        'label' => __('Image'),
                    ],
                    'attributes' => [
                        'type'        => 'file',
                        'description' => '',
                    ],
                ]
            );
        }
        // extra_seo
        $this->add(
            [
                'name'    => 'extra_seo',
                'type'    => 'fieldset',
                'options' => [
                    'label' => __('SEO options'),
                ],
            ]
        );
        // seo_title
        $this->add(
            [
                'name'       => 'seo_title',
                'options'    => [
                    'label' => __('SEO Title'),
                ],
                'attributes' => [
                    'type'        => 'textarea',
                    'rows'        => '2',
                    'cols'        => '40',
                    'description' => '',
                ],
            ]
        );
        // seo_keywords
        $this->add(
            [
                'name'       => 'seo_keywords',
                'options'    => [
                    'label' => __('SEO Keywords'),
                ],
                'attributes' => [
                    'type'        => 'textarea',
                    'rows'        => '2',
                    'cols'        => '40',
                    'description' => '',
                ],
            ]
        );
        // seo_description
        $this->add(
            [
                'name'       => 'seo_description',
                'options'    => [
                    'label' => __('SEO Description'),
                ],
                'attributes' => [
                    'type'        => 'textarea',
                    'rows'        => '3',
                    'cols'        => '40',
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
