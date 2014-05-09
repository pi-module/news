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

class AttachFilter extends InputFilter
{
    public function __construct()
    {
        // id
        $this->add(array(
            'name' => 'id',
            'required' => true,
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
        // story
        /* $this->add(array(
            'name' => 'story',
            'required' => true,
        )); */
        // status
        $this->add(array(
            'name' => 'status',
            'required' => true,
        ));
    }
}