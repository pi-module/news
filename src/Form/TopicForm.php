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

class TopicForm extends BaseForm
{
    protected $options;

    public function __construct($name = null, $options = array())
    {
        $this->category = array(0 => 'Root');
        $this->module = Pi::service('module')->current();
        $this->thumbUrl = (isset($options['thumbUrl'])) ? $options['thumbUrl'] : '';
        $this->removeUrl = empty($options['removeUrl']) ? '' : $options['removeUrl'];
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new TopicFilter;
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
        // pid
        $this->add(array(
            'name' => 'pid',
            'type' => 'Module\News\Form\Element\Topic',
            'options' => array(
                'label' => __('Parent Topic'),
                'module' => $this->module,
                'topic' => $this->topic,
            ),
            'attributes' => array(
                'size' => 1,
                'multiple' => 0,
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
        // slug
        $this->add(array(
            'name' => 'slug',
            'options' => array(
                'label' => __('slug'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // description
        $this->add(array(
            'name' => 'description',
            'options' => array(
                'label' => __('Description'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '5',
                'cols' => '40',
                
                'description' => '',
            )
        ));
        // description_footer
        $this->add(array(
            'name' => 'description_footer',
            'options' => array(
                'label' => __('Footer description'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '5',
                'cols' => '40',
                
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
                    1 => __('Published'),
                    2 => __('Pending review'),
                    3 => __('Draft'),
                    4 => __('Private'),
                ),
            ),
        ));
        // style
        $this->add(array(
            'name' => 'style',
            'type' => 'select',
            'options' => array(
                'label' => __('Topic Style'),
                'value_options' => array(
                    'news' => __('News'),
                    'list' => __('List'),
                    'table' => __('Table'),
                    'media' => __('Media'),
                    'spotlight' => __('Spotlight'),
                ),
            ),
            'attributes' => array(
                'description' => '',
            ),
        ));
        // Image
        if ($this->thumbUrl) {
            $this->add(array(
                'name' => 'imageview',
                'options' => array(
                    'label' => __('Image'),
                ),
                'attributes' => array(
                    'type' => 'image',
                    'src' => $this->thumbUrl,
                    'disabled' => true,
                    'description' => '',
                )
            ));
            $this->add(array(
                'name' => 'remove',
                'options' => array(
                    'label' => __('Remove image'),
                ),
                'attributes' => array(
                    'type' => 'button',
                    'class' => 'btn btn-danger btn-sm',
                    'data-toggle' => 'button',
                    'data-link' => $this->removeUrl,
                )
            ));
            $this->add(array(
                'name' => 'image',
                'attributes' => array(
                    'type' => 'hidden',
                ),
             ));
        } else {
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
        }
        // extra_seo
        $this->add(array(
            'name' => 'extra_seo',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('SEO options'),
            ),
        ));
        // seo_title
        $this->add(array(
            'name' => 'seo_title',
            'options' => array(
                'label' => __('SEO Title'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // seo_keywords
        $this->add(array(
            'name' => 'seo_keywords',
            'options' => array(
                'label' => __('SEO Keywords'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // seo_description
        $this->add(array(
            'name' => 'seo_description',
            'options' => array(
                'label' => __('SEO Description'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // extra
        $this->add(array(
            'name' => 'extra_settings',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('Extra options'),
            ),
        ));
        // show_config
        $this->add(array(
            'name' => 'show_config',
            'type' => 'select',
            'options' => array(
                'label' => __('Extra Config Options Type'),
                'value_options' => array(
                    'topic' => __('Use topic options'),
                    'module' => __('Use module options'),
                ),
            ),
            'attributes' => array(
                'description' => __('Use topic option whit select topic options or set it to module to use module option'),
            ),
        ));
        // perpage
        $this->add(array(
            'name' => 'show_perpage',
            'options' => array(
                'label' => __('Perpage'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // columns
        $this->add(array(
            'name' => 'show_columns',
            'type' => 'select',
            'options' => array(
                'label' => __('Columns'),
                'value_options' => array(
                    1 => __('One column'),
                    2 => __('Two columns'),
                    3 => __('Three columns'),
                    4 => __('Four columns'),
                ),
            ),
            'attributes' => array(
                'description' => '',
            )
        ));
        // show_topic
        $this->add(array(
            'name' => 'show_topic',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Topic'),
            ),
            'attributes' => array(
                'description' => '',
            )
        ));
        // show_topicinfo
        $this->add(array(
            'name' => 'show_topicinfo',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Topic Information'),
            ),
            'attributes' => array(
                'description' => '',
            )
        ));
        // show_writer
        $this->add(array(
            'name' => 'show_writer',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show writer'),
            ),
            'attributes' => array(
                'description' => '',
            )
        ));
        // show_date
        $this->add(array(
            'name' => 'show_date',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Date'),
            ),
            'attributes' => array(
                'description' => '',
            )
        ));
        // show_pdf
        $this->add(array(
            'name' => 'show_pdf',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show DPF'),
            ),
            'attributes' => array(
                'description' => '',
            )
        ));
        // show_print
        $this->add(array(
            'name' => 'show_print',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Print'),
            ),
            'attributes' => array(
                'description' => '',
            )
        ));
        // show_mail
        $this->add(array(
            'name' => 'show_mail',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Mail'),
            ),
            'attributes' => array(
                'description' => '',
            )
        ));
        // show_hits
        $this->add(array(
            'name' => 'show_hits',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Hits'),
            ),
            'attributes' => array(
                'description' => '',
            )
        ));
        // show_tag
        $this->add(array(
            'name' => 'show_tag',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Tags'),
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