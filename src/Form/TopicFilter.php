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
use Laminas\InputFilter\InputFilter;

class TopicFilter extends InputFilter
{
    public function __construct($option)
    {
        // pid
        $this->add(
            [
                'name'     => 'pid',
                'required' => true,
            ]
        );

        // title
        $this->add(
            [
                'name'     => 'title',
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // slug
        $this->add(
            [
                'name'       => 'slug',
                'required'   => false,
                'filters'    => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    new \Module\News\Validator\SlugDuplicate(
                        [
                            'module' => Pi::service('module')->current(),
                            'table'  => 'topic',
                            'id'     => $option['id'],
                        ]
                    ),
                ],
            ]
        );

        // text_summary
        $this->add(
            [
                'name'     => 'text_summary',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // text_description
        $this->add(
            [
                'name'     => 'text_description',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // image
        $this->add(
            [
                'name'     => 'image',
                'required' => false,
            ]
        );

        // status
        $this->add(
            [
                'name'     => 'status',
                'required' => true,
            ]
        );

        // style
        $this->add(
            [
                'name'     => 'style',
                'required' => false,
            ]
        );

        // type
        $this->add(
            [
                'name'     => 'type',
                'required' => false,
            ]
        );

        // display_order
        $this->add(
            [
                'name'     => 'display_order',
                'required' => false,
            ]
        );

        // seo_title
        $this->add(
            [
                'name'     => 'seo_title',
                'required' => false,
            ]
        );

        // seo_keywords
        $this->add(
            [
                'name'     => 'seo_keywords',
                'required' => false,
            ]
        );

        // seo_description
        $this->add(
            [
                'name'     => 'seo_description',
                'required' => false,
            ]
        );

        // show_config
        $this->add(
            [
                'name'     => 'show_config',
                'required' => false,
            ]
        );

        // show_perpage
        $this->add(
            [
                'name'     => 'show_perpage',
                'required' => false,
            ]
        );

        // show_columns
        $this->add(
            [
                'name'     => 'show_columns',
                'required' => false,
            ]
        );

        // set_page
        $this->add(
            [
                'name'     => 'set_page',
                'required' => false,
            ]
        );

        // show_order_link
        $this->add(
            [
                'name'     => 'show_order_link',
                'required' => false,
            ]
        );

        // show_topic
        $this->add(
            [
                'name'     => 'show_topic',
                'required' => false,
            ]
        );

        // show_topicinfo
        $this->add(
            [
                'name'     => 'show_topicinfo',
                'required' => false,
            ]
        );

        // show_date
        $this->add(
            [
                'name'     => 'show_date',
                'required' => false,
            ]
        );

        // show_pdf
        $this->add(
            [
                'name'     => 'show_pdf',
                'required' => false,
            ]
        );

        // show_print
        $this->add(
            [
                'name'     => 'show_print',
                'required' => false,
            ]
        );

        // show_mail
        $this->add(
            [
                'name'     => 'show_mail',
                'required' => false,
            ]
        );

        // show_hits
        $this->add(
            [
                'name'     => 'show_hits',
                'required' => false,
            ]
        );

        // show_tag
        $this->add(
            [
                'name'     => 'show_tag',
                'required' => false,
            ]
        );

        // show_subid
        $this->add(
            [
                'name'     => 'show_subid',
                'required' => false,
            ]
        );

        // view_position
        $this->add(
            [
                'name'     => 'view_position',
                'required' => false,
            ]
        );

        // attach
        $this->add(
            [
                'name'     => 'attach',
                'required' => false,
            ]
        );

        // attach_title
        $this->add(
            [
                'name'     => 'attach_title',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );

        // attach_link
        $this->add(
            [
                'name'     => 'attach_link',
                'required' => false,
            ]
        );
    }
}