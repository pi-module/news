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
    public function __construct($name = null, $option = array())
    {
        $this->option = $option;
        $this->module = Pi::service('module')->current();
        $this->thumbUrl = (isset($option['thumbUrl'])) ? $option['thumbUrl'] : '';
        $this->removeUrl = empty($option['removeUrl']) ? '' : $option['removeUrl'];
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new StoryFilter($this->option);
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
        // extra_text
        $this->add(array(
            'name' => 'extra_text',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('Text options'),
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
                'description' => __('Used as story URL value : must be unique, short, and user oriented'),
            )
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
                'description' => __('Just shown on story list and blocks'),
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
                'description' => __('Only shown on story page'),
            )
        ));
        // text_html
        if ($this->option['admin_text_extra']) {
            $this->add(array(
                'name' => 'text_html',
                'options' => array(
                    'label' => __('Extra html text'),
                ),
                'attributes' => array(
                    'type' => 'textarea',
                    'rows' => '5',
                    'cols' => '40',
                    'description' => __('Set custom html code to show under main text'),
                )
            ));
        }
        // extra_main
        $this->add(array(
            'name' => 'extra_main',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('Main options'),
            ),
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
        if (!$this->option['admin_deactivate_view']) {
            $option = array(
                'text' => __('Text'),
                'article' => __('Article'),
                'magazine' => __('Magazine'),
                'gallery' => __('Gallery album'),
                'image' => __('Single image'),
                'media' => __('Media'),
                'download' => __('Download'),
                'post' => __('Blog post'),
            );
        } else {
            $option = array(
                'post' => __('Blog post'),
            );
        }
        $this->add(array(
            'name' => 'type',
            'type' => 'select',
            'options' => array(
                'label' => __('Type'),
                'value_options' => $option,
            ),
            'attributes' => array(
                'required' => true,
            ),
        ));
        // Check is not blog
        if ($this->option['type'] != 'post') {
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
        }
        // time_publish
        if ($this->option['admin_time_publish']) {
            $this->add(array(
                'name' => 'time_publish',
                'option' => array(
                    'label' => __('Publish time'),
                ),
                'attributes' => array(
                    'type' => 'text',
                    'description' => '',
                )
            ));
        }
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
        // extra_seo
        $this->add(array(
            'name' => 'extra_media',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('Media options'),
            ),
        ));

        $this->add(array(
            'name' => 'main_image',
            'type' => 'Module\Media\Form\Element\Media',
            'options' => array(
                'label' => __('Main image'),
                'module' => 'news',
            ),
        ));

        $this->add(array(
            'name' => 'additional_images',
            'type' => 'Module\Media\Form\Element\Media',
            'options' => array(
                'label' => __('Additional images'),
                'media_gallery' => true,
                'module' => 'news',
            ),
        ));

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
                'label' => __('Meta Title'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '2',
                'cols' => '40',
                'description' => __('Displayed in search Engine result pages as Title : between 10 to 70 character. If empty, will be popuated automaticaly by main title value'),
            )
        ));
        // seo_keywords
        $this->add(array(
            'name' => 'seo_keywords',
            'options' => array(
                'label' => __('Meta Keywords'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '2',
                'cols' => '40',
                'description' => __('Not used anymore by search engines : between 5 to 12 words / left it empty, it will be automaticaly populated with main title values'),
            )
        ));
        // seo_description
        $this->add(array(
            'name' => 'seo_description',
            'options' => array(
                'label' => __('Meta Description'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '3',
                'cols' => '40',
                'description' => __('Displayed in search Engine result pages : quick summary to incitate user to click, between 80 to 160 character'),
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
        if (!empty($this->option['role'])) {
            // extra_author
            $this->add(array(
                'name' => 'extra_author',
                'type' => 'fieldset',
                'options' => array(
                    'label' => __('Authors'),
                ),
            ));
            foreach ($this->option['role'] as $role) {
                $this->add(array(
                    'name' => $role['name'],
                    'type' => 'Module\News\Form\Element\Author',
                    'options' => array(
                        'label' => $role['title'],
                        'list' => $this->option['author'],
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
                'class' => 'btn btn-primary',
            )
        ));
    }
}