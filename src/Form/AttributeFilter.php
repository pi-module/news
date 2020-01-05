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
use Zend\InputFilter\InputFilter;

class AttributeFilter extends InputFilter
{
    public function __construct($options)
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
        // name
        $this->add(
            [
                'name'       => 'name',
                'required'   => true,
                'filters'    => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    new \Module\News\Validator\NameDuplicate(
                        [
                            'module' => Pi::service('module')->current(),
                            'table'  => 'field',
                        ]
                    ),
                ],
            ]
        );
        // topic
        $this->add(
            [
                'name'     => 'topic',
                'required' => true,
            ]
        );
        // status
        $this->add(
            [
                'name'     => 'status',
                'required' => true,
            ]
        );
        // position
        $this->add(
            [
                'name'     => 'position',
                'required' => true,
            ]
        );
        // type
        /* $this->add(array(
            'name' => 'type',
            'required' => true,
        )); */
        if ($options['type'] == 'select') {
            // data
            $this->add(
                [
                    'name'     => 'data',
                    'required' => false,
                ]
            );
            // default
            $this->add(
                [
                    'name'     => 'default',
                    'required' => false,
                ]
            );
        }
        // information
        $this->add(
            [
                'name'     => 'information',
                'required' => false,
            ]
        );
        // icon
        $this->add(
            [
                'name'     => 'icon',
                'required' => false,
            ]
        );
        // search
        $this->add(
            [
                'name'     => 'search',
                'required' => false,
            ]
        );
    }
}