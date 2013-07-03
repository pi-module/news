<?php
/**
 * Prune form
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

class PruneForm extends BaseForm
{
    protected $options;

    public function __construct($name = null, $module)
    {
        $this->module = $module;
        parent::__construct($name);
    }

    public function init()
    {
        // date
        $this->add(array(
            'name' => 'date',
            'options' => array(
                'label' => __('All contacts Before'),
            ),
            'attributes' => array(
                'type' => 'text',
                'value' => date('Y-m-d'),
            )
        ));
        // topic
        $this->add(array(
            'name' => 'topic',
            'type' => 'Module\News\Form\Element\Topic',
            'options' => array(
                'label' => __('Topic'),
                'module' => $this->module,
                'topic' => '',
            ),
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