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

class ModeratorFilter extends InputFilter
{
    public function __construct()
    {
        // manager
        $this->add(array(
            'name' => 'manager',
            'required' => true,
        ));
        // topic
        $this->add(array(
            'name' => 'topic',
            'required' => true,
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'required' => false,
        ));
    }
}