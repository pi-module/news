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
use Pi\Form\Form as BaseForm;

class PageForm extends BaseForm
{
    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    public function init()
    {
        // confirm
        $this->add(array(
            'name' => 'confirm',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Confirm to remove all topic pages'),
            ),
            'attributes' => array(
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