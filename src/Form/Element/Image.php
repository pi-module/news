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
use Zend\Form\Element\Image as ZendImage;

class Image extends ZendImage
{
    /**
     * @return array
     */
    public function getAttributes()
    {
        $this->Attributes = [
            'class' => 'img-thumbnail item-img',
            'src'   => $this->attributes['src'],
        ];
        return $this->Attributes;
    }
}