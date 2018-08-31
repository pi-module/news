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

class StoryAdditionalFilter extends InputFilter
{
    public function __construct($option = [])
    {
        // Set attribute position
        $position = Pi::api('attribute', 'news')->attributePositionForm();
        // id
        $this->add(
            [
                'name'     => 'id',
                'required' => false,
            ]
        );
        // Set attribute
        if (!empty($option['field'])) {
            foreach ($position as $key => $value) {
                if (!empty($option['field'][$key])) {
                    foreach ($option['field'][$key] as $field) {
                        $this->add(
                            [
                                'name'     => $field['id'],
                                'required' => false,
                            ]
                        );
                    }
                }
            }
        }
    }
}