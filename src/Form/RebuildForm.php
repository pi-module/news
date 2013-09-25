<?php
/**
 * Rebuild form
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Copyright (c) Pi Engine http://www.xoopsengine.org
 * @license         http://www.xoopsengine.org/license New BSD License
 * @author          Hossein Azizabadi <azizabadi@faragostaresh.com>
 * @since           3.0
 * @package         Module\News
 * @subpackage      Form
 * @version         $Id$
 */

namespace Module\News\Form;

use Pi;
use Pi\Form\Form as BaseForm;

class RebuildForm extends BaseForm
{
    public function init()
    {
        $this->add(array(
            'name' => 'rebuild',
            'type' => 'select',
            'options' => array(
                'label' => __('Rebuild'),
                'value' => 'slug',
                'value_options' => array(
                    'slug' => __('slug'),
                    'keywords' => __('Meta Keywords'),
                    'description' => __('Meta Description'),
                ),
            ),
        ));
        // start date
        $this->add(array(
            'name' => 'start',
            'options' => array(
                'label' => __('Start date'),
            ),
            'attributes' => array(
                'type' => 'date',
                'value' => date('Y-m-d'),
                'description' => '',
            )
        ));
        // end date
        $this->add(array(
            'name' => 'end',
            'options' => array(
                'label' => __('End date'),
            ),
            'attributes' => array(
                'type' => 'date',
                'value' => date('Y-m-d'),
                'description' => '',
            )
        ));
        // Save
        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'value' => __('Submit'),
            )
        ));
    }
}	