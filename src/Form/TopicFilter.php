<?php
/**
 * topic form
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
        // body
        $this->add(array(
            'name' => 'body',
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
        // keywords
        $this->add(array(
            'name' => 'keywords',
            'required' => false,
        ));
        // description
        $this->add(array(
            'name' => 'description',
            'required' => false,
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'required' => true,
        ));
        // inlist
        $this->add(array(
            'name' => 'inlist',
            'required' => false,
        ));
        // topic_type
        $this->add(array(
            'name' => 'topic_type',
            'required' => false,
        ));
        // topic_homepage
        $this->add(array(
            'name' => 'topic_homepage',
            'required' => false,
        ));
        // topic_style
        $this->add(array(
            'name' => 'topic_style',
            'required' => false,
        ));
        // perpage
        $this->add(array(
            'name' => 'perpage',
            'required' => false,
        ));
        // columns
        $this->add(array(
            'name' => 'columns',
            'required' => false,
        ));
        // showtopic
        $this->add(array(
            'name' => 'showtopic',
            'required' => false,
        ));
        // showtopicinfo
        $this->add(array(
            'name' => 'showtopicinfo',
            'required' => false,
        ));
        // showauthor
        $this->add(array(
            'name' => 'showauthor',
            'required' => false,
        ));
        // showdate
        $this->add(array(
            'name' => 'showdate',
            'required' => false,
        ));
        // showpdf
        $this->add(array(
            'name' => 'showpdf',
            'required' => false,
        ));
        // showprint
        $this->add(array(
            'name' => 'showprint',
            'required' => false,
        ));
        // showmail
        $this->add(array(
            'name' => 'showmail',
            'required' => false,
        ));
        // shownav
        $this->add(array(
            'name' => 'shownav',
            'required' => false,
        ));
        // showhits
        $this->add(array(
            'name' => 'showhits',
            'required' => false,
        ));
        // showcoms
        $this->add(array(
            'name' => 'showcoms',
            'required' => false,
        ));
    }
}