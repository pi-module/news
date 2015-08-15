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

class StoryFilter extends InputFilter
{
    public function __construct($option = array())
    {
        // id
        $this->add(array(
            'name' => 'id',
            'required' => false,
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
        // subtitle
        $this->add(array(
            'name' => 'subtitle',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // slug
        $this->add(array(
            'name' => 'slug',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
            'validators' => array(
                new \Module\News\Validator\SlugDuplicate(array(
                    'module' => Pi::service('module')->current(),
                    'table' => 'story',
                )),
            ),
        ));
        // topic
        $this->add(array(
            'name' => 'topic',
            'required' => true,
        ));
        // topic_main
        $this->add(array(
            'name' => 'topic_main',
            'required' => true,
        ));
        // text_summary
        $this->add(array(
            'name' => 'text_summary',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // text_description
        $this->add(array(
            'name' => 'text_description',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // important
        $this->add(array(
            'name' => 'important',
            'required' => false,
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'required' => true,
        ));
        // type
        $this->add(array(
            'name' => 'type',
            'required' => true,
        ));
        // image
        $this->add(array(
            'name' => 'image',
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
        // tag
        if (Pi::service('module')->isActive('tag')) {
            $this->add(array(
                'name' => 'tag',
                'required' => false,
            ));
        }
        // Set role
        if (!empty($option['role'])) {
            foreach ($option['role'] as $role) {
                $this->add(array(
                    'name' => $role['name'],
                    'required' => false,
                ));
            }
        }
    }
}