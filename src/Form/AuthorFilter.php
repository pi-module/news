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

class AuthorFilter extends InputFilter
{
    public function __construct($option)
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
                            'table'  => 'author',
                            'id'     => $option['id'],
                        ]
                    ),
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
    }
}
