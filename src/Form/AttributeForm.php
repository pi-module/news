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

class AttributeForm extends BaseForm
{
    public function __construct($name = null)
    {
        $this->module = Pi::service('module')->current();
        $this->position = Pi::api('attribute', 'news')->attributePositionForm();
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new AttributeFilter;
        }
        return $this->filter;
    }

    public function init()
    {
        // id
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        // title
        $this->add(array(
            'name' => 'title',
            'options' => array(
                'label' => __('Title'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
                'required'  => true,
            )
        ));
        // name
        $this->add(array(
            'name' => 'name',
            'options' => array(
                'label' => __('Name'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => __('Set name for call anywhere'),
                'required'  => true,
            )
        ));
        // topic
        $this->add(array(
            'name' => 'topic',
            'type' => 'Module\News\Form\Element\Topic',
            'options' => array(
                'label' => __('Topic'),
                'module' => $this->module,
            ),
            'attributes' => array(
                'required'  => true,
                'size'      => 5,
                'multiple'  => 1,
            )
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'type' => 'select',
            'options' => array(
                'label' => __('Status'),
                'value_options' => array(
                    1 => __('Online'),
                    0 => __('Offline'),
                ),
            ),
            'attributes' => array(
                'required'  => true,
            )
        ));
        // position
        $this->add(array(
            'name' => 'position',
            'type' => 'select',
            'options' => array(
                'label' => __('Set'),
                'value_options' => $this->position,
            ),
            'attributes' => array(
                'required'  => true,
            )
        ));
        // type
        $this->add(array(
            'name' => 'type',
            'type' => 'select',
            'options' => array(
                'label' => __('Type'),
                'value_options'    => array(
                    'text'      => __('Text : type text content'),
                    'link'      => __('Link : add url for click'),
                    'video'     => __('Video : add flv or mp4 url for play on video player'),
                    'audio'     => __('Audio : add mp3 url for play on audio player'),
                    'file'      => __('File : add file link for download'),
                    'currency'  => __('Currency : add view price for anything'),
                    'date'      => __('Date : add date for view'),
                    'number'    => __('Number : add number'),
                    'select'    => __('Select : add select box for choose'),
                    'checkbox'  => __('Checkbox : add check box for 0 or 1'),
                ),
            ),
            'attributes' => array(
                'required'  => true,
            )
        ));
        // data
        $this->add(array(
            'name' => 'data',
            'options' => array(
                'label' => __('General data'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '5',
                'cols' => '40',
                'description' => '',
            )
        ));
        // default
        $this->add(array(
            'name' => 'default',
            'options' => array(
                'label' => __('Default data'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // information
        $this->add(array(
            'name' => 'information',
            'options' => array(
                'label' => __('Extra information'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => __('Put URL for click by user'),
            )
        ));
        // icon
        $this->add(array(
            'name' => 'icon',
            'options' => array(
                'label' => __('Icon'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => __('Use fontawesome.io icons, and set icon name like fa-home'),
            )
        ));
        // search
        $this->add(array(
            'name' => 'search',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Search'),
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