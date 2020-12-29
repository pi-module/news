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
use Pi\Form\Form as BaseForm;

class RebuildForm extends BaseForm
{
    public function init()
    {
        $this->add(
            [
                'name'    => 'rebuild',
                'type'    => 'select',
                'options' => [
                    'label'         => __('Rebuild'),
                    'value'         => '',
                    'value_options' => [
                        ''                => '',
                        'slug'            => __('Slug'),
                        'slug_title'      => __('Replace slug by title'),
                        'slug_id'         => __('Replace slug by id'),
                        'seo_title'       => __('SEO Title'),
                        'seo_keywords'    => __('SEO Keywords'),
                        'seo_description' => __('SEO Description'),
                    ],
                ],
            ]
        );
        // start date
        $this->add(
            [
                'name'       => 'start',
                'options'    => [
                    'label' => __('Start date'),
                ],
                'attributes' => [
                    'type'        => 'date',
                    'value'       => date('Y-m-d', strtotime('-1 week')),
                    'description' => '',
                ],
            ]
        );
        // end date
        $this->add(
            [
                'name'       => 'end',
                'options'    => [
                    'label' => __('End date'),
                ],
                'attributes' => [
                    'type'        => 'date',
                    'value'       => date('Y-m-d'),
                    'description' => '',
                ],
            ]
        );
        // Save
        $this->add(
            [
                'name'       => 'submit',
                'type'       => 'submit',
                'attributes' => [
                    'value' => __('Submit'),
                ],
            ]
        );
    }
}
