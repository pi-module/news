<?php
/**
 * News module config
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

return array(
    // Item block
    'item' => array(
        'title' => __('Item'),
        'description' => '',
        'render' => array('block', 'item'),
        'template' => 'item',
        'config' => array(
            'topicid' => array(
                'title' => __('Topic'),
                'description' => __(''),
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'number' => array(
                'title' => __('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showdesc' => array(
                'title' => __('Shwo text'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showdate' => array(
                'title' => __('Show date'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showhits' => array(
                'title' => __('Show hits'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showimage' => array(
                'title' => __('Show Images'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showmore' => array(
                'title' => __('Show More link'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'order' => array(
                'title' => __('Order'),
                'description' => '',
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'publish DESC' => __('Publish'),
                            'hits DESC' => __('Hits'),
                            'comments DESC' => __('Comments'),
                        ),
                    ),
                ),
                'filter' => 'string',
                'value' => 'publish DESC',
            ),
        ),
        'access' => array(
            'guest' => 1,
            'member' => 1,
        ),
    ),

    // List block
    'list' => array(
        'title' => __('list'),
        'description' => '',
        'render' => array('block', 'item'),
        'template' => 'list',
        'config' => array(
            'topicid' => array(
                'title' => __('topic'),
                'description' => __(''),
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'number' => array(
                'title' => __('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 10,
            ),
            'showdate' => array(
                'title' => __('Show date'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'showhits' => array(
                'title' => __('Show hits'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'order' => array(
                'title' => __('Order'),
                'description' => '',
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'publish DESC' => __('Publish'),
                            'hits DESC' => __('Hits'),
                            'comments DESC' => __('Comments'),
                        ),
                    ),
                ),
                'filter' => 'string',
                'value' => 'publish DESC',
            ),
        ),
        'access' => array(
            'guest' => 1,
            'member' => 1,
        ),
    ),

    // Table block
    'table' => array(
        'title' => __('Table'),
        'description' => '',
        'render' => array('block', 'item'),
        'template' => 'table',
        'config' => array(
            'topicid' => array(
                'title' => __('topic'),
                'description' => __(''),
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'number' => array(
                'title' => __('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showdesc' => array(
                'title' => __('Shwo text'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'showdate' => array(
                'title' => __('Show date'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showhits' => array(
                'title' => __('Show hits'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'order' => array(
                'title' => __('Order'),
                'description' => '',
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'publish DESC' => __('Publish'),
                            'hits DESC' => __('Hits'),
                            'comments DESC' => __('Comments'),
                        ),
                    ),
                ),
                'filter' => 'string',
                'value' => 'publish DESC',
            ),
        ),
        'access' => array(
            'guest' => 1,
            'member' => 1,
        ),
    ),


    // Slotlight block
    'spotlight' => array(
        'title' => __('Spotlight'),
        'description' => '',
        'render' => array('block', 'spotlight'),
        'template' => 'spotlight',
        'config' => array(
            'topicid' => array(
                'title' => __('topic'),
                'description' => __(''),
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'number' => array(
                'title' => __('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 5,
            ),
            'subspotlight' => array(
                'title' => __('Number of sub spotlight'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 4,
            ),
            'showdesc' => array(
                'title' => __('Shwo text'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showdate' => array(
                'title' => __('Show date'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showhits' => array(
                'title' => __('Show hits'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showimage' => array(
                'title' => __('Show Images'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'order' => array(
                'title' => __('Order'),
                'description' => '',
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'publish DESC' => __('Publish'),
                            'hits DESC' => __('Hits'),
                            'comments DESC' => __('Comments'),
                        ),
                    ),
                ),
                'filter' => 'string',
                'value' => 'publish DESC',
            ),
        ),
        'access' => array(
            'guest' => 1,
            'member' => 1,
        ),
    ),

    // topic block
    'topic' => array(
        'title' => __('List of topic'),
        'description' => '',
        'render' => array('block', 'topic'),
        'template' => 'topic',
        'config' => array(
            'topicid' => array(
                'title' => __('Topic'),
                'description' => __(''),
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'showdesc' => array(
                'title' => __('Shwo text'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showimage' => array(
                'title' => __('Show Images'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
        ),
        'access' => array(
            'guest' => 1,
            'member' => 1,
        ),
    ),
);