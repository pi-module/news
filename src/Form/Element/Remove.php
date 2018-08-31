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
use Zend\Form\Element\Button as ZendButton;

class Remove extends ZendButton
{
    /**
     * @return array
     */
    public function getAttributes()
    {
        $this->Attributes = [
            'class'       => 'btn btn-danger btn-sm remove-image',
            'data-toggle' => 'button',
            'data-link'   => $this->attributes['link'],
        ];
        return $this->Attributes;
    }
}