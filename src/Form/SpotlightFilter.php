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

class SpotlightFilter extends InputFilter
{
    public function __construct()
    {
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
