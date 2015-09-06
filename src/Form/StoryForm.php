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

class StoryForm extends BaseForm
{
    public function __construct($name = null, $options = array())
    {
        $this->module = Pi::service('module')->current();
        $this->author = $options['author'];
        $this->role = $options['role'];
        $this->thumbUrl = (isset($options['thumbUrl'])) ? $options['thumbUrl'] : '';
        $this->removeUrl = empty($options['removeUrl']) ? '' : $options['removeUrl'];
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new StoryFilter;
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
                'required' => true,
            )
        ));
        // subtitle
        $this->add(array(
            'name' => 'subtitle',
            'options' => array(
                'label' => __('Subtitle'),
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
        // topic
        $this->add(array(
            'name' => 'topic',
            'type' => 'Module\News\Form\Element\Topic',
            'options' => array(
                'label' => __('Topic'),
                'module' => $this->module,
                'topic' => 'full',
                'required' => true,
            ),
        ));
        // topic_main
        $this->add(array(
            'name' => 'topic_main',
            'type' => 'Module\News\Form\Element\Topic',
            'options' => array(
                'label' => __('Main topic'),
                'module' => $this->module,
                'topic' => '',
            ),
            'attributes' => array(
                'required' => true,
                'size' => 1,
                'multiple' => 0,
                'description' => __('Just use for breadcrumbs and mobile apps'),
            ),
        ));
        // text_summary
        $this->add(array(
            'name' => 'text_summary',
            'options' => array(
                'label' => __('Short text'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '5',
                'cols' => '40',
                'description' => '',
            )
        ));
        // text_description
        $this->add(array(
            'name' => 'text_description',
            'options' => array(
                'label' => __('Main text'),
                'editor' => 'html',
                'set' => '',
            ),
            'attributes' => array(
                'type' => 'editor',
                'description' => '',
            )
        ));
        // important
        $this->add(array(
            'name' => 'important',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Important'),
            ),
            'attributes' => array(
                'description' => '',
            )
        ));
        // spotlight
        $this->add(array(
            'name' => 'spotlight',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Add as spotlight'),
            ),
            'attributes' => array(
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
                    5 => __('Remove'),
                ),
            ),
            'attributes' => array(
                'required' => true,
            ),
        ));
        // type
        $this->add(array(
            'name' => 'type',
            'type' => 'select',
            'options' => array(
                'label' => __('Type'),
                'value_options' => array(
                    'text' => __('Text'),
                    'gallery' => __('Gallery'),
                    'media' => __('Media'),
                    'download' => __('Download'),
                ),
            ),
            'attributes' => array(
                'required' => true,
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
                    'label' => __('Upload image'),
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
        // tag
        if (Pi::service('module')->isActive('tag')) {
            $this->add(array(
                'name' => 'tag',
                'type' => 'tag',
                'options' => array(
                    'label' => __('Tags'),
                ),
                'attributes' => array(
                    'id' => 'tag',
                    'description' => __('Use `|` as delimiter to separate tag terms'),
                )
            ));
        }
        // Set extra author
        if (!empty($this->role)) {
            // extra_author
            $this->add(array(
                'name' => 'extra_author',
                'type' => 'fieldset',
                'options' => array(
                    'label' => __('Authors'),
                ),
            ));
            foreach ($this->role as $role) {
                $this->add(array(
                    'name' => $role['name'],
                    'type' => 'Module\News\Form\Element\Author',
                    'options' => array(
                        'label' => $role['title'],
                        'list' => $this->author,
                    ),
                ));
            }
        }
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