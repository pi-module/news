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

class StoryFilter extends InputFilter
{
    public function __construct($option = [])
    {
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
        // subtitle
        $this->add(
            [
                'name'     => 'subtitle',
                'required' => false,
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
                            'table'  => 'story',
                            'id'     => $option['id'],
                        ]
                    ),
                ],
            ]
        );
        // Check is not blog
        if ($option['type'] != 'post') {
            // topic
            $this->add(
                [
                    'name'     => 'topic',
                    'required' => true,
                ]
            );
            // topic_main
            $this->add(
                [
                    'name'     => 'topic_main',
                    'required' => true,
                ]
            );
        }
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
        // text_html
        if ($option['admin_text_extra']) {
            $this->add(
                [
                    'name'     => 'text_html',
                    'required' => false,
                    'filters'  => [
                        [
                            'name' => 'StringTrim',
                        ],
                    ],
                ]
            );
        }
        // time_publish
        if ($option['admin_time_publish']) {
            $this->add(
                [
                    'name'       => 'time_publish',
                    'required'   => false,
                    'filters'    => [
                        [
                            'name' => 'StringTrim',
                        ],
                    ],
                    'validators' => [
                        new \Module\News\Validator\TimePublish,
                    ],
                ]
            );
        }
        // important
        $this->add(
            [
                'name'     => 'important',
                'required' => false,
            ]
        );
        // spotlight
        $this->add(
            [
                'name'     => 'spotlight',
                'required' => false,
            ]
        );
        // status
        if ($option['user_allow_confirm']) {
            $this->add(
                [
                    'name'     => 'status',
                    'required' => true,
                ]
            );
        }
        // type
        $this->add(
            [
                'name'     => 'type',
                'required' => true,
            ]
        );

        // Main image
        $this->add(
            [
                'name'     => 'main_image',
                'required' => false,
            ]
        );

        // Main image
        $this->add(
            [
                'name'     => 'additional_images',
                'required' => false,
            ]
        );

        // image
        $this->add(
            [
                'name'     => 'image',
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
        // tag
        if (Pi::service('module')->isActive('tag')) {
            $this->add(
                [
                    'name'     => 'tag',
                    'required' => false,
                ]
            );
        }
        // Set role
        if (!empty($option['role'])) {
            foreach ($option['role'] as $role) {
                $this->add(
                    [
                        'name'     => $role['name'],
                        'required' => false,
                    ]
                );
            }
        }
    }
}
