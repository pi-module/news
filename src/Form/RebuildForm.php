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

class RebuildForm extends BaseForm
{
    public function init()
    {
        $this->add(array(
            'name' => 'rebuild',
            'type' => 'select',
            'options' => array(
                'label' => __('Rebuild'),
                'value' => '',
                'value_options' => array(
                    '' => '',
                    'slug' => __('Slug'),
                    'slug_title' => __('Replace slug by title'),
                    'slug_id' => __('Replace slug by id'),
                    'seo_title' => __('SEO Title'),
                    'seo_keywords' => __('SEO Keywords'),
                    'seo_description' => __('SEO Description'),
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
                'value' => date('Y-m-d', strtotime('-1 week')),
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