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