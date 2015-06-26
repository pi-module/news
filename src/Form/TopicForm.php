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
    public function __construct($name = null, $options = array())
    {
        $this->module = Pi::service('module')->current();
        $this->category = array(0 => 'Root');
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
                'topic' => '',
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
        // text_description
        $this->add(array(
            'name' => 'text_description',
            'options' => array(
                'label' => __('Description'),
                'editor' => 'html',
                'set' => '',
            ),
            'attributes' => array(
                'type' => 'editor',
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
                    5 => __('Delete'),
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
                    'topic' => __('Topic'),
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
                'type' => 'Module\News\Form\Element\Image',
                'options' => array(//'label' => __('Image'),
                ),
                'attributes' => array(
                    'src' => $this->thumbUrl,
                ),
            ));
            $this->add(array(
                'name' => 'remove',
                'type' => 'Module\News\Form\Element\Remove',
                'options' => array(
                    'label' => __('Remove image'),
                ),
                'attributes' => array(
                    'link' => $this->removeUrl,
                ),
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
                'type' => 'textarea',
                'rows' => '2',
                'cols' => '40',
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
                'type' => 'textarea',
                'rows' => '2',
                'cols' => '40',
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
                'type' => 'textarea',
                'rows' => '3',
                'cols' => '40',
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
        // set_page
        $this->add(array(
            'name' => 'set_page',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Set as system page'),
            ),
            'attributes' => array(
                'description' => '',
            )
        ));
        // show_order_link
        $this->add(array(
            'name' => 'show_order_link',
            'type' => 'select',
            'options' => array(
                'label' => __('Story order'),
                'value_options' => array(
                    'publishDESC' => __('Publish time DESC'),
                    'publishASC' => __('Publish time ASC'),
                    'random' => __('Random'),
                ),
            ),
            'attributes' => array(
                'description' => __('Story list order options'),
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
        // show_subid
        $this->add(array(
            'name' => 'show_subid',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Subtopic stories on main topic'),
            ),
            'attributes' => array(
                'description' => '',
            )
        ));
        // attach_link
        $this->add(array(
            'name' => 'attach_link',
            'options' => array(
                'label' => __('Attach file link'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // attach_title
        $this->add(array(
            'name' => 'attach_title',
            'options' => array(
                'label' => __('Attach file title'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // attach_download_count
        $this->add(array(
            'name' => 'attach_download_count',
            'attributes' => array(
                'type' => 'hidden',
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