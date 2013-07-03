<?php
/**
 * Topic form
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Copyright (c) Pi Engine http://www.xoopsengine.org
 * @license         http://www.xoopsengine.org/license New BSD License
 * @author          Hossein Azizabadi <azizabadi@faragostaresh.com>
 * @since           3.0
 * @package         Module\News
 * @version         $Id$
 */

namespace Module\News\Form;

use Pi;
use Pi\Form\Form as BaseForm;

class TopicForm extends BaseForm
{
    protected $options;

    public function __construct($name = null, $module, $options = array())
    {
        $this->topic = array(0 => ' ');
        $this->module = $module;
        $this->imageurl = $options['imageurl'];
        $this->removeurl = empty($options['removeurl']) ? '' : $options['removeurl'];
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
        // alias
        $this->add(array(
            'name' => 'alias',
            'options' => array(
                'label' => __('Alias'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // body
        $this->add(array(
            'name' => 'body',
            'options' => array(
                'label' => __('Text'),
                'editor' => 'html',
                'set' => '',
            ),
            'attributes' => array(
                'type' => 'editor',
                'description' => '',
            )
        ));
        // Image
        if (isset($this->imageurl)) {
            $this->add(array(
                'name' => 'imageview',
                'options' => array(
                    'label' => __('Image'),
                ),
                'attributes' => array(
                    'type' => 'image',
                    'src' => $this->imageurl,
                    'height' => '200',
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
                    'class' => 'btn btn-danger btn-small',
                    'data-toggle' => 'button',
                    'data-link' => $this->removeurl,
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
        // keywords
        $this->add(array(
            'name' => 'keywords',
            'options' => array(
                'label' => __('Keywords'),
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
                    1 => __('Published'),
                    2 => __('Pending review'),
                    3 => __('Draft'),
                    4 => __('Private'),
                ),
            ),
        ));
        // inlist
        $this->add(array(
            'name' => 'inlist',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('In List'),
            ),
            'attributes' => array(
                'description' => '',
                'value' => '1',
            )
        ));
        // extra
        $this->add(array(
            'name' => 'extra',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('Extra options'),
            ),
        ));
        // topic_type
        $this->add(array(
            'name' => 'topic_type',
            'type' => 'select',
            'options' => array(
                'label' => __('Extra Options Type'),
                'value_options' => array(
                    'topic' => __('Use topic options'),
                    'module' => __('Use module options'),
                ),
            ),
            'attributes' => array(
                'description' => __('Use topic option whit select topic options or set it to module to use module option'),
                'id' => 'group1',
            ),
        ));
        // topic_homepage
        $this->add(array(
            'name' => 'topic_homepage',
            'type' => 'select',
            'options' => array(
                'label' => __('Homepage'),
                'value_options' => array(
                    'type1' => __('All storys from this topic and all subtopics'),
                    'type2' => __('All storys from just this topic'),
                    'type3' => __('All storys from just all subtopics'),
                ),
            ),
            'attributes' => array(
                'description' => '',
                'class' => 'group1',
            ),
        ));
        // topic_style
        $this->add(array(
            'name' => 'topic_style',
            'type' => 'select',
            'options' => array(
                'label' => __('Showtype'),
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
                'class' => 'group1',
            ),
        ));
        // perpage
        $this->add(array(
            'name' => 'perpage',
            'options' => array(
                'label' => __('Perpage'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
                'class' => 'group1',
            )
        ));
        // columns
        $this->add(array(
            'name' => 'columns',
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
                'class' => 'group1',
            )
        ));
        // showtopic
        $this->add(array(
            'name' => 'showtopic',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Topic'),
            ),
            'attributes' => array(
                'description' => '',
                'class' => 'group1',
            )
        ));
        // showtopicinfo
        $this->add(array(
            'name' => 'showtopicinfo',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Topic Information'),
            ),
            'attributes' => array(
                'description' => '',
                'class' => 'group1',
            )
        ));
        // showauthor
        $this->add(array(
            'name' => 'showauthor',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Author'),
            ),
            'attributes' => array(
                'description' => '',
                'class' => 'group1',
            )
        ));
        // showdate
        $this->add(array(
            'name' => 'showdate',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Date'),
            ),
            'attributes' => array(
                'description' => '',
                'class' => 'group1',
            )
        ));
        // showpdf
        $this->add(array(
            'name' => 'showpdf',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show DPF'),
            ),
            'attributes' => array(
                'description' => '',
                'class' => 'group1',
            )
        ));
        // showprint
        $this->add(array(
            'name' => 'showprint',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Print'),
            ),
            'attributes' => array(
                'description' => '',
                'class' => 'group1',
            )
        ));
        // showmail
        $this->add(array(
            'name' => 'showmail',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Mail'),
            ),
            'attributes' => array(
                'description' => '',
                'class' => 'group1',
            )
        ));
        // shownav
        $this->add(array(
            'name' => 'shownav',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Nav'),
            ),
            'attributes' => array(
                'description' => '',
                'class' => 'group1',
            )
        ));
        // showhits
        $this->add(array(
            'name' => 'showhits',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Hits'),
            ),
            'attributes' => array(
                'description' => '',
                'class' => 'group1',
            )
        ));
        // showcoms
        $this->add(array(
            'name' => 'showcoms',
            'type' => 'checkbox',
            'options' => array(
                'label' => __('Show Coms'),
            ),
            'attributes' => array(
                'description' => '',
                'class' => 'group1',
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