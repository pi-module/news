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

class ExtraForm extends BaseForm
{
    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new ExtraFilter;
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
        ));
        // type
        $this->add(array(
            'name' => 'type',
            'type' => 'select',
            'options' => array(
                'label' => __('Type'),
                'value_options' => array(
                    'text' => __('Text : type text content'),
                    'link' => __('Link : add url for click'),
                    'video' => __('Video : add flv or mp4 url for play on video player'),
                    'audio' => __('Audio : add mp3 url for play on audio player'),
                    'file' => __('File : add file link for download'),
                    'currency' => __('Currency : add view price for anything'),
                    'date' => __('Date : add date for view'),
                    'number' => __('Number : add number'),
                    'select' => __('Select : add select box for choose'),
                ),
            ),
        ));
        // value
        $this->add(array(
            'name' => 'value',
            'options' => array(
                'label' => __('Value'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '5',
                'cols' => '40',
                
                'description' => '',
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
        // image
        $this->add(array(
            'name' => 'image',
            'options' => array(
                'label' => __('Image'),
            ),
            'attributes' => array(
                'type' => 'file',
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