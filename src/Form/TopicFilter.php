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
use Zend\InputFilter\InputFilter;

class TopicFilter extends InputFilter
{
    public function __construct()
    {
        // id
        $this->add(array(
            'name' => 'id',
            'required' => false,
        ));
        // pid
        $this->add(array(
            'name' => 'pid',
            'required' => true,
        ));
        // title
        $this->add(array(
            'name' => 'title',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // slug
        $this->add(array(
            'name'          => 'slug',
            'required'      => false,
            'filters'       => array(
                array(
                    'name'  => 'StringTrim',
                ),
            ),
            'validators'    => array(
                new \Module\News\Validator\SlugDuplicate(array(
                    'module'            => Pi::service('module')->current(),
                    'table'             => 'topic',
                )),
            ),
        ));
        // description
        $this->add(array(
            'name' => 'description',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // image
        $this->add(array(
            'name' => 'image',
            'required' => false,
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'required' => true,
        ));
        // style
        $this->add(array(
            'name' => 'style',
            'required' => false,
        ));
        // seo_title
        $this->add(array(
            'name' => 'seo_title',
            'required' => false,
        ));
        // seo_keywords
        $this->add(array(
            'name' => 'seo_keywords',
            'required' => false,
        ));
        // seo_description
        $this->add(array(
            'name' => 'seo_description',
            'required' => false,
        ));
        // show_config
        $this->add(array(
            'name' => 'show_config',
            'required' => false,
        ));
        // show_perpage
        $this->add(array(
            'name' => 'show_perpage',
            'required' => false,
        ));
        // show_columns
        $this->add(array(
            'name' => 'show_columns',
            'required' => false,
        ));
        // show_topic
        $this->add(array(
            'name' => 'show_topic',
            'required' => false,
        ));
        // show_topicinfo
        $this->add(array(
            'name' => 'show_topicinfo',
            'required' => false,
        ));
        // show_writer
        $this->add(array(
            'name' => 'show_writer',
            'required' => false,
        ));
        // show_date
        $this->add(array(
            'name' => 'show_date',
            'required' => false,
        ));
        // show_pdf
        $this->add(array(
            'name' => 'show_pdf',
            'required' => false,
        ));
        // show_print
        $this->add(array(
            'name' => 'show_print',
            'required' => false,
        ));
        // show_mail
        $this->add(array(
            'name' => 'show_mail',
            'required' => false,
        ));
        // show_hits
        $this->add(array(
            'name' => 'show_hits',
            'required' => false,
        ));
        // show_tag
        $this->add(array(
            'name' => 'show_tag',
            'required' => false,
        ));
    }
}