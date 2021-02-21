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

class TopicForm extends BaseForm
{
    public function __construct($name = null, $option = [])
    {
        $this->option    = $option;
        $this->module    = Pi::service('module')->current();
        $this->category  = [0 => 'Root'];

        d($this->option);

        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new TopicFilter($this->option);
        }
        return $this->filter;
    }

    public function init()
    {
        // pid
        $this->add(
            [
                'name'       => 'pid',
                'type'       => 'Module\News\Form\Element\Topic',
                'options'    => [
                    'label'  => __('Parent Topic'),
                    'module' => $this->module,
                    'topic'  => '',
                ],
                'attributes' => [
                    'size'     => 1,
                    'multiple' => 0,
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
                    'description' => __('Just show on story list and blocks'),
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
                        5 => __('Delete'),
                    ],
                ],
            ]
        );

        // style
        $this->add(
            [
                'name'       => 'style',
                'type'       => 'select',
                'options'    => [
                    'label'         => __('Topic Style'),
                    'value_options' => [
                        'news'      => __('News'),
                        'list'      => __('List'),
                        'table'     => __('Table'),
                        'media'     => __('Media'),
                        'spotlight' => __('Spotlight'),
                        'topic'     => __('Topic'),
                    ],
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );

        // type
        $this->add(
            [
                'name'       => 'type',
                'type'       => 'select',
                'options'    => [
                    'label'         => __('Topic type'),
                    'value_options' => [
                        'general' => __('General'),
                        'event'   => __('Just event module'),
                        'blog'    => __('Just blog module'),
                    ],
                ],
                'attributes' => [
                    'description' => __('Set type for use as general or special module'),
                ],
            ]
        );

        // Image
        if (isset($this->option['thumbUrl']) && !empty($this->option['thumbUrl'])) {
            $this->add(
                [
                    'name'       => 'imageview',
                    'type'       => 'Module\News\Form\Element\Image',
                    'options'    => [//'label' => __('Image'),
                    ],
                    'attributes' => [
                        'src' => $this->option['thumbUrl'],
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
                        'link' => $this->option['removeUrl'],
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

        // display_order
        $this->add(
            [
                'name'       => 'display_order',
                'options'    => [
                    'label' => __('Display order'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                    'required'    => false,
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
        // extra
        $this->add(
            [
                'name'    => 'extra_settings',
                'type'    => 'fieldset',
                'options' => [
                    'label' => __('Extra options'),
                ],
            ]
        );
        // show_config
        $this->add(
            [
                'name'       => 'show_config',
                'type'       => 'select',
                'options'    => [
                    'label'         => __('Extra Config Options Type'),
                    'value_options' => [
                        'topic'  => __('Use topic options'),
                        'module' => __('Use module options'),
                    ],
                ],
                'attributes' => [
                    'description' => __('Use topic option whit select topic options or set it to module to use module option'),
                ],
            ]
        );
        // perpage
        $this->add(
            [
                'name'       => 'show_perpage',
                'options'    => [
                    'label' => __('Perpage'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                ],
            ]
        );
        // columns
        $this->add(
            [
                'name'       => 'show_columns',
                'type'       => 'select',
                'options'    => [
                    'label'         => __('Columns'),
                    'value_options' => [
                        1 => __('One column'),
                        2 => __('Two columns'),
                        3 => __('Three columns'),
                        4 => __('Four columns'),
                    ],
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // set_page
        $this->add(
            [
                'name'       => 'set_page',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Set as system page'),
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // show_order_link
        $this->add(
            [
                'name'       => 'show_order_link',
                'type'       => 'select',
                'options'    => [
                    'label'         => __('Story order'),
                    'value_options' => [
                        'publishDESC' => __('Publish time DESC'),
                        'publishASC'  => __('Publish time ASC'),
                        'random'      => __('Random'),
                    ],
                ],
                'attributes' => [
                    'description' => __('Story list order options'),
                ],
            ]
        );
        // show_topic
        $this->add(
            [
                'name'       => 'show_topic',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Show Topic'),
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // show_topicinfo
        $this->add(
            [
                'name'       => 'show_topicinfo',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Show Topic Information'),
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // show_date
        $this->add(
            [
                'name'       => 'show_date',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Show Date'),
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // show_pdf
        $this->add(
            [
                'name'       => 'show_pdf',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Show DPF'),
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // show_print
        $this->add(
            [
                'name'       => 'show_print',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Show Print'),
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // show_mail
        $this->add(
            [
                'name'       => 'show_mail',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Show Mail'),
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // show_hits
        $this->add(
            [
                'name'       => 'show_hits',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Show Hits'),
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // show_tag
        $this->add(
            [
                'name'       => 'show_tag',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Show Tags'),
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // show_subid
        $this->add(
            [
                'name'       => 'show_subid',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Show Subtopic stories on main topic'),
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // view_position
        $this->add(
            [
                'name'       => 'view_position',
                'type'       => 'select',
                'options'    => [
                    'label'         => __('View position for api'),
                    'value_options' => [
                        'both'   => __('Both'),
                        'top'    => __('Top'),
                        'side'   => __('Side'),
                        'hidden' => __('Hidden'),
                    ],
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // extra_attach
        $this->add(
            [
                'name'    => 'extra_attach',
                'type'    => 'fieldset',
                'options' => [
                    'label' => __('Attach file options'),
                ],
            ]
        );
        // attach
        $this->add(
            [
                'name'       => 'attach',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Check for attach file'),
                ],
                'attributes' => [
                    'description' => '',
                ],
            ]
        );
        // attach_title
        $this->add(
            [
                'name'       => 'attach_title',
                'options'    => [
                    'label' => __('File title'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                ],
            ]
        );
        // attach_link
        $this->add(
            [
                'name'       => 'attach_link',
                'options'    => [
                    'label' => __('File link'),
                ],
                'attributes' => [
                    'type'        => 'text',
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
