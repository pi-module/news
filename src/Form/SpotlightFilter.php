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

class SpotlightFilter extends InputFilter
{
    public function __construct()
    {
        // id
        $this->add(
            [
                'name'     => 'id',
                'required' => false,
            ]
        );
        // story
        $this->add(
            [
                'name'     => 'story',
                'required' => true,
            ]
        );
        // topic
        $this->add(
            [
                'name'     => 'topic',
                'required' => true,
            ]
        );
        // time_publish
        $this->add(
            [
                'name'     => 'time_publish',
                'required' => true,
            ]
        );
        // time_expire
        $this->add(
            [
                'name'     => 'time_expire',
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
    }
}