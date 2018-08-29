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

class StoryForm extends BaseForm
{
    public function __construct($name = null, $option = [])
    {
        $this->option    = $option;
        $this->module    = Pi::service('module')->current();
        $this->thumbUrl  = (isset($option['thumbUrl'])) ? $option['thumbUrl'] : '';
        $this->removeUrl = empty($option['removeUrl']) ? '' : $option['removeUrl'];
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new StoryFilter($this->option);
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
        // extra_text
        $this->add(
            [
                'name'    => 'extra_text',
                'type'    => 'fieldset',
                'options' => [
                    'label' => __('Text options'),
                ],
            ]
        );
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
        // subtitle
        $this->add(
            [
                'name'       => 'subtitle',
                'options'    => [
                    'label' => __('Subtitle'),
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
                    'description' => __('Used as story URL value : must be unique, short, and user oriented'),
                ],
            ]
        );
        // text_summary
        $this->add(
            [
                'name'       => 'text_summary',
                'options'    => [
                    'label' => __('Short text'),
                ],
                'attributes' => [
                    'type'        => 'textarea',
                    'rows'        => '5',
                    'cols'        => '40',
                    'description' => __('Just shown on story list and blocks'),
                ],
            ]
        );
        // text_description
        $this->add(
            [
                'name'       => 'text_description',
                'options'    => [
                    'label'  => __('Main text'),
                    'editor' => 'html',
                    'set'    => '',
                ],
                'attributes' => [
                    'type'        => 'editor',
                    'description' => __('Only shown on story page'),
                ],
            ]
        );
        // text_html
        if ($this->option['admin_text_extra']) {
            $this->add(
                [
                    'name'       => 'text_html',
                    'options'    => [
                        'label' => __('Extra html text'),
                    ],
                    'attributes' => [
                        'type'        => 'textarea',
                        'rows'        => '5',
                        'cols'        => '40',
                        'description' => __('Set custom html code to show under main text'),
                    ],
                ]
            );
        }
        // extra_main
        $this->add(
            [
                'name'    => 'extra_main',
                'type'    => 'fieldset',
                'options' => [
                    'label' => __('Main options'),
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
                        1 => __('Published'),
                        2 => __('Pending review'),
                        3 => __('Draft'),
                        4 => __('Private'),
                        5 => __('Remove'),
                    ],
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]
        );
        // type
        if (!$this->option['admin_deactivate_view']) {
            $option = [
                'text'     => __('Text'),
                'article'  => __('Article'),
                'magazine' => __('Magazine'),
                'gallery'  => __('Gallery album'),
                'image'    => __('Single image'),
                'media'    => __('Media'),
                'download' => __('Download'),
                'post'     => __('Blog post'),
            ];
        } else {
            $option = [
                'post' => __('Blog post'),
            ];
        }
        $this->add(
            [
                'name'       => 'type',
                'type'       => 'select',
                'options'    => [
                    'label'         => __('Type'),
                    'value_options' => $option,
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]
        );
        // Check is not blog
        if ($this->option['type'] != 'post') {
            // topic
            $this->add(
                [
                    'name'    => 'topic',
                    'type'    => 'Module\News\Form\Element\Topic',
                    'options' => [
                        'label'    => __('Topic'),
                        'module'   => $this->module,
                        'topic'    => 'full',
                        'required' => true,
                    ],
                ]
            );
            // topic_main
            $this->add(
                [
                    'name'       => 'topic_main',
                    'type'       => 'Module\News\Form\Element\Topic',
                    'options'    => [
                        'label'  => __('Main topic'),
                        'module' => $this->module,
                        'topic'  => '',
                    ],
                    'attributes' => [
                        'required'    => true,
                        'size'        => 1,
                        'multiple'    => 0,
                        'description' => __('Just use for breadcrumbs and mobile apps'),
                    ],
                ]
            );
        }
        // time_publish
        if ($this->option['admin_time_publish']) {
            $this->add(
                [
                    'name'       => 'time_publish',
                    'option'     => [
                        'label' => __('Publish time'),
                    ],
                    'attributes' => [
                        'type'        => 'text',
                        'description' => '',
                    ],
                ]
            );
        }
        // important
        $this->add(
            [
                'name'       => 'important',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Important'),
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // spotlight
        $this->add(
            [
                'name'       => 'spotlight',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Add as spotlight'),
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // extra_seo
        $this->add(
            [
                'name'    => 'extra_media',
                'type'    => 'fieldset',
                'options' => [
                    'label' => __('Media options'),
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'main_image',
                'type'    => 'Module\Media\Form\Element\Media',
                'options' => [
                    'label'  => __('Main image'),
                    'module' => 'news',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'additional_images',
                'type'    => 'Module\Media\Form\Element\Media',
                'options' => [
                    'label'         => __('Additional images'),
                    'media_gallery' => true,
                    'module'        => 'news',
                ],
            ]
        );

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
                    'label' => __('Meta Title'),
                ],
                'attributes' => [
                    'type'        => 'textarea',
                    'rows'        => '2',
                    'cols'        => '40',
                    'description' => __(
                        'Displayed in search Engine result pages as Title : between 10 to 70 character. If empty, will be popuated automaticaly by main title value'
                    ),
                ],
            ]
        );
        // seo_keywords
        $this->add(
            [
                'name'       => 'seo_keywords',
                'options'    => [
                    'label' => __('Meta Keywords'),
                ],
                'attributes' => [
                    'type'        => 'textarea',
                    'rows'        => '2',
                    'cols'        => '40',
                    'description' => __(
                        'Not used anymore by search engines : between 5 to 12 words / left it empty, it will be automaticaly populated with main title values'
                    ),
                ],
            ]
        );
        // seo_description
        $this->add(
            [
                'name'       => 'seo_description',
                'options'    => [
                    'label' => __('Meta Description'),
                ],
                'attributes' => [
                    'type'        => 'textarea',
                    'rows'        => '3',
                    'cols'        => '40',
                    'description' => __('Displayed in search Engine result pages : quick summary to incitate user to click, between 80 to 160 character'),
                ],
            ]
        );
        // tag
        if (Pi::service('module')->isActive('tag')) {
            $this->add(
                [
                    'name'       => 'tag',
                    'type'       => 'tag',
                    'options'    => [
                        'label' => __('Tags'),
                    ],
                    'attributes' => [
                        'id'          => 'tag',
                        'description' => __('Use `|` as delimiter to separate tag terms'),
                    ],
                ]
            );
        }
        // Set extra author
        if (!empty($this->option['role'])) {
            // extra_author
            $this->add(
                [
                    'name'    => 'extra_author',
                    'type'    => 'fieldset',
                    'options' => [
                        'label' => __('Authors'),
                    ],
                ]
            );
            foreach ($this->option['role'] as $role) {
                $this->add(
                    [
                        'name'    => $role['name'],
                        'type'    => 'Module\News\Form\Element\Author',
                        'options' => [
                            'label' => $role['title'],
                            'list'  => $this->option['author'],
                        ],
                    ]
                );
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
}