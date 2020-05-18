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

namespace Module\News\Form\Element;

use Pi;
use Laminas\Form\Element\Button as LaminasButton;

class Attach extends LaminasButton
{
    /**
     * @return array
     */
    public function getAttributes()
    {
        $this->Attributes = [
            'class'       => 'btn btn-success btn-lg',
            'data-toggle' => 'modal',
            'data-target' => '#attachFile',
        ];
        return $this->Attributes;
    }
}