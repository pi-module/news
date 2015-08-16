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
use Zend\InputFilter\InputFilter;

class AttributeFilter extends InputFilter
{
    public function __construct($options)
    {
        // id
        $this->add(array(
            'name' => 'id',
            'required' => false,
        ));
        // title
        $this->add(array(
            'name' => 'title',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // name
        $this->add(array(
            'name' => 'name',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
            'validators' => array(
                new \Module\News\Validator\NameDuplicate(array(
                    'module' => Pi::service('module')->current(),
                    'table' => 'field',
                )),
            ),
        ));
        // topic
        $this->add(array(
            'name' => 'topic',
            'required' => true,
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'required' => true,
        ));
        // position
        $this->add(array(
            'name' => 'position',
            'required' => true,
        ));
        // type
        /* $this->add(array(
            'name' => 'type',
            'required' => true,
        )); */
        if ($options['type'] == 'select') {
            // data
            $this->add(array(
                'name' => 'data',
                'required' => false,
            ));
            // default
            $this->add(array(
                'name' => 'default',
                'required' => false,
            ));
        }
        // information
        $this->add(array(
            'name' => 'information',
            'required' => false,
        ));
        // icon
        $this->add(array(
            'name' => 'icon',
            'required' => false,
        ));
        // search
        $this->add(array(
            'name' => 'search',
            'required' => false,
        ));
    }
}